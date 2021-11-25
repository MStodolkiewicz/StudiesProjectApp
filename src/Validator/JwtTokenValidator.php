<?php

namespace App\Validator;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class JwtTokenValidator extends ConstraintValidator
{
    private $tokenManager;

    public function __construct(JWTTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            $token = $this->tokenManager->parse($value);

            if (null !== $constraint->action) {
                if (!isset($token['action'])) {
                    $this->context->buildViolation($constraint->noActionMessage)
                        ->addViolation();
                    return;
                }

                if ($constraint->action !== $token['action']) {
                    $this->context->buildViolation($constraint->differentActionMessage)
                        ->addViolation();
                    return;
                }
            }
        } catch(JWTDecodeFailureException $e) {
            switch($e->getReason()) {
                case JWTDecodeFailureException::EXPIRED_TOKEN:
                    $this->context->buildViolation($constraint->expiredMessage)
                        ->addViolation();
                    return;

                case JWTDecodeFailureException::UNVERIFIED_TOKEN:
                    $this->context->buildViolation($constraint->unverifiedMessage)
                        ->addViolation();
                    return;

                case JWTDecodeFailureException::INVALID_TOKEN:
                default:
                    $this->context->buildViolation($constraint->invalidMessage)
                        ->addViolation();
                    return;
            }
        }
    }
}
