<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Validation of jwt token contents
 * @Annotation
 * @Target({"PROPERTY"})
 */
class JwtToken extends Constraint
{
    public $action = null;

    public $invalidMessage = 'The token is invalid.';
    public $expiredMessage = 'The token is expired.';
    public $unverifiedMessage = 'The token is unverified.';
    public $noActionMessage = 'This token does not have an action.';
    public $differentActionMessage = 'This token does not have the correct action.';

    public function __construct($options = null, array $groups = null, $payload = null, $action = null, $invalidMessage = null, $expiredMessage = null, $unverifiedMessage = null, $noActionMessage = null, $differentActionMessage = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->action = $action ?? $this->action;
        $this->invalidMessage = $invalidMessage ?? $this->invalidMessage;
        $this->expiredMessage = $expiredMessage ?? $this->expiredMessage;
        $this->unverifiedMessage = $unverifiedMessage ?? $this->unverifiedMessage;
        $this->noActionMessage = $noActionMessage ?? $this->noActionMessage;
        $this->differentActionMessage = $differentActionMessage ?? $this->differentActionMessage;
    }
}