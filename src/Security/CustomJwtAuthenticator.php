<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class CustomJwtAuthenticator extends JWTAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return false !== $this->getTokenExtractor()->extract($request);
    }

}