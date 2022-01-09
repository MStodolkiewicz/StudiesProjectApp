<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@user.pl');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user,'userStrongPass123'));
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.pl');
        $admin->setRoles(['ROLE_ADMIN','ROLE_USER']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin,'adminStrongPass123'));
        $manager->persist($admin);

        $manager->flush();


    }
}
