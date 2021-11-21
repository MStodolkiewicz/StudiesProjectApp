<?php

namespace App\Validator;

use App\Entity\Rate;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RateEditValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;
    private $rateRepository;


    public function __construct(EntityManagerInterface $entityManager, Security $security, RateRepository $rateRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->rateRepository = $rateRepository;
    }

    /**
     * @param Rate $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint RateEdit */

        if (!$value instanceof Rate) {
            throw new \LogicException(sprintf("Object passed to this validator must be of type %s", Rate::class));
        }

        $currentUser = $this->security->getUser();
        $originalRate = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);




        if($this->security->isGranted("ROLE_ADMIN")) return;
            //New Rate being created
            if(!$originalRate){
                $isRateAlreadyCreated = true == $this->rateRepository->findOneBy(['product' => $value->getProduct()->getId(),'user' => $currentUser->getId()]);
                if($isRateAlreadyCreated){
                    $this->context->buildViolation('You cannot add new rate for this product! Please edit existing one.')
                        ->addViolation();
                }
            //Rate being edited
            }else{
                if($value->getProduct()->getId() != $originalRate['product_id']){
                    $this->context->buildViolation('Cannot change product !')
                        ->addViolation();
                }
                if ($value->getUser() !== $currentUser) {
                    $this->context->buildViolation('Rates can be given only by and as currently logged user')
                        ->addViolation();
                }
            }




//        foreach (array_keys($originalRate) as $key) {
//            $methodName = 'get' . ucfirst($key);
//            if (method_exists($rate, $methodName) && $originalRate[$key] != $rate->{$methodName}()){
//
//            }
//        }


        // TODO: implement the validation here
//        $this->context->buildViolation($constraint->message)
//            ->addViolation();
    }
}
