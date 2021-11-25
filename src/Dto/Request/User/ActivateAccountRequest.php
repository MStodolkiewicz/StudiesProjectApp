<?php

namespace App\Dto\Request\User;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\JwtToken;
use App\Constant\JwtActions;

class ActivateAccountRequest
{
    /**
     * @Assert\NotBlank
     * @JwtToken(action = JwtActions::ACTIVATE_ACCOUNT)
     */

    public $token;
}