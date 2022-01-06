<?php

namespace App\Validator;

use App\Entity\Intake;
use App\Repository\IntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsMealTypeProperValidator extends ConstraintValidator
{
    const MEAL_TYPES = [
        'Breakfast',
        'Brunch',
        'Lunch',
        'Tea',
        'Dinner',
    ];

    public function __construct()
    {

    }

    /**
     * @param Intake $value
     * @param Constraint $constraint
     */

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint IntakeEdit */
        if(!in_array($value,self::MEAL_TYPES)){
            $this->context->buildViolation(sprintf("MealType can only be of type %s",implode(",",self::MEAL_TYPES)))->addViolation();
        }
    }
}
