<?php

namespace App\Validator;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IngredientEditValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;
    private $ingredientRepository;


    public function __construct(EntityManagerInterface $entityManager, Security $security, IngredientRepository $ingredientRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->ingredientRepository = $ingredientRepository;
    }

    /**
     * @param Ingredient $value
     * @param Constraint $constraint
     */

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint IngredientEdit */

        if (!$value instanceof Ingredient) {
            throw new \LogicException(sprintf("Object passed to this validator must be of type %s", Ingredient::class));
        }

        $currentUser = $this->security->getUser();
        $originalIntake = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if($this->security->isGranted("ROLE_ADMIN")) return;

        if(!$originalIntake){
            $isRateAlreadyCreated = true == $this->ingredientRepository->findOneBy(['name'=> $value->getName()]);
            if($isRateAlreadyCreated){
                $this->context->buildViolation('This ingredient is already created.')
                    ->addViolation();
            }

        }else{
            if($value->getProduct()->getId() != $originalIntake['product_id']){
                $this->context->buildViolation('Cannot change product !')
                    ->addViolation();
            }
            if ($value->getProduct()->getUser() !== $currentUser) {
                $this->context->buildViolation('Not an owner of this object.')
                    ->addViolation();
            }
        }

    }
}
