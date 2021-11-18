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

        $currentUser = $this->security->getUser();
        $originalRate = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if($this->security->isGranted("ROLE_ADMIN")) return;
        // dodaj logike ktora sprawi ze uzytkownik nie bedzie mogl tworzyc nowej oceny danemu przedmiotowi
        // jezeli juz wczesniej ocenil dany produkt
        // w wyzej wymienionym przypadku uzytkownik powinien edytowac, a nie tworzyc ocene.
        // naprawdopodobniej trzeba bedzie uzyc rateReposiotry ew. productRepository ?

//        if($value->getUser() !== $currentUser){
//            $this->context->buildViolation('Rates can be given only by and as currently logged user')
//                ->addViolation();
//        }
        if(!$value->getProduct() || !$value->getValue()){
            //skipping any validation throws here because of @Assert/NotNull and @Assert/NotBlank in Rate Entity Class file.

        }else{
            if(!$originalRate){
                $isRateAlreadyCreated = true == $this->rateRepository->findOneBy(['product' => $value->getProduct()->getId(),'user' => $currentUser->getId()]);
                if($isRateAlreadyCreated){
                    $this->context->buildViolation('You cannot add new rate for this product! Please edit existing one.')
                        ->addViolation();
                }
            }
        }


//        foreach (array_keys($originalRate) as $key) {
//            $methodName = 'get' . ucfirst($key);
//            if (method_exists($value, $methodName) && $originalRate[$key] != $value->{$methodName}()){
//
//            }
//        }


        // TODO: implement the validation here
//        $this->context->buildViolation($constraint->message)
//            ->addViolation();
    }
}
