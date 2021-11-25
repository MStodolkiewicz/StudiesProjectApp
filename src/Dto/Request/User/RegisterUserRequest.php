<?php

namespace App\Dto\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $username;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="1024")
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="8")
     */
    public $password;
}