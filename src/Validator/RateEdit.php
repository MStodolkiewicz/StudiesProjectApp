<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class RateEdit extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'You were trying to execute unauthorised action on Rate';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
