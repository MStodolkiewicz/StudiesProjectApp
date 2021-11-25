<?php

namespace App\Application\Service;

use App\Constant\JwtActions;
use App\Dto\Request\User\ActivateAccountRequest;
use App\Dto\Request\User\RegisterUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    private $entityManager;

    private $userRepository;

    private $userPasswordHasher;

    private $emailService;

    private $tokenManager;


    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EmailService $emailService, JWTTokenManagerInterface $tokenManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->emailService = $emailService;
        $this->tokenManager = $tokenManager;
    }

    public function exists($email,$username){
        null !== $this->userRepository->findUserByEmailOrUsername($email,$username);
    }

    public function register(RegisterUserRequest $request,$host): User
    {
        // tutaj sprawdzac czy nie istnieje przypadkiem ?

        if($this->userRepository->findUserByEmailOrUsername($request->email, $request->username)){
            throw new \Exception('User with given email or username already exists');
        }

        $user = new User();
        $user->setEmail($request->email);
        $user->setUsername($request->username);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $request->password));
        $user->setRoles([User::ROLE_USER]);
        $user->setIsVerified(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();



        $token = $this->tokenManager->createFromPayload($user, ['sub' => $user->getId(), 'action' => JwtActions::ACTIVATE_ACCOUNT]);

        $this->emailService->sendToUser('account/welcome', $user, 'Confirm your account', [
            'activationLink' => sprintf('%s/activate-account?token=%s', $host, $token), // TODO: Make this a parameter
            'user' => $user,
        ]);

        return $user;
    }

    public function activate(ActivateAccountRequest $request): ?User
    {
        $decodedToken = $this->tokenManager->parse($request->token);
        $user = $this->userRepository->find($decodedToken['sub']);
        if (null === $user) {
            throw new \Exception(sprintf('The user %s was not found', $decodedToken['sub']));
        }

        if($user->isVerified()){
            return $user;
        }

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}