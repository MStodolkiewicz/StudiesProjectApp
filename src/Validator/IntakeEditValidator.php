<?php

namespace App\Validator;

use App\Entity\Intake;
use App\Repository\IntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IntakeEditValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;
    const MEAL_TYPES = [
        'Breakfast',
        'Brunch',
        'Lunch',
        'Tea',
        'Dinner',
    ];

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param Intake $value
     * @param Constraint $constraint
     */

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint IntakeEdit */

        if (!$value instanceof Intake) {
            throw new \LogicException(sprintf("Object passed to this validator must be of type %s", Intake::class));
        }

        $currentUser = $this->security->getUser();
        $originalIntake = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if ($this->security->isGranted("ROLE_ADMIN")) return;

        if ($originalIntake) {
            if ($value->getUser() !== $currentUser) {
                $this->context->buildViolation('Not an owner of this object')
                    ->addViolation();
            }
        if(!in_array($value->getMealType(),self::MEAL_TYPES)){
            $this->context->buildViolation(sprintf("MealType can only be of type %s",implode(",",self::MEAL_TYPES)))->addViolation();
        }
        }
    }
}
