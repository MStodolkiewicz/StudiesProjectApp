<?php

namespace App\Controller\User;

use App\Application\Service\UserService;
use App\Constant\JwtActions;
use App\Dto\Request\User\ActivateAccountRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivateAccountController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(Request $request)
    {
        $this->userService->activate($request->attributes->get('dto'));
    }

    /**
     * @Route("/activate-account", name="activate_account")
     */
    public function activateAccount(Request $request)
    {
        $activateAccountRequest = new ActivateAccountRequest();
        $activateAccountRequest->token = $request->get('token');
        $activateAccountRequest->action = JwtActions::ACTIVATE_ACCOUNT;

        $user = $this->userService->activate($activateAccountRequest);

        if($user->isVerified()){
            return new Response("Account verified");
        }

        return new Response("Account couldn't be verified.");

    }

}