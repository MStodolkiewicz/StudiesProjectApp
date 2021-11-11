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

        $originalRate = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if($this->security->isGranted("ROLE_ADMIN")) return;
        // dodaj logike ktora sprawi ze uzytkownik nie bedzie mogl tworzyc nowej oceny danemu przedmiotowi
        // jezeli juz wczesniej ocenil dany produkt
        // w wyzej wymienionym przypadku uzytkownik powinien edytowac, a nie tworzyc ocene.
        // naprawdopodobniej trzeba bedzie uzyc rateReposiotry ew. productRepository ?
        if($value->getProduct() && !$originalRate){

//        $product = $this->rateRepository->findBy(['user_id' => $this->security->getUser()->getId(),'product_id' => $value->getProduct()->getId()]);

        }



//        dd($value->getProduct());

//        if(!$originalRate) return;
//        dd($originalRate);

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
