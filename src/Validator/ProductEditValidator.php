<?php

namespace App\Validator;

use App\Entity\KcalPerOneGramOf;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductEditValidator extends ConstraintValidator
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param Product $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ProductEdit */

        if (!$value instanceof Product) {
            throw new \LogicException(sprintf("Object passed to this validator must be of type %s", Product::class));
        }

        $currentUser = $this->security->getUser();
        $originalProduct = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if($this->security->isGranted("ROLE_ADMIN")) return;
        // New Product being created
        $minimalKcal = $value->getFat() * KcalPerOneGramOf::FAT + $value->getCarbohydrates() * KcalPerOneGramOf::CARBOHYDRATES + $value->getProteins() * KcalPerOneGramOf::PROTEIN;

        if($value->getKcal() < $minimalKcal){
            $this->context->buildViolation('Number of supplied calories is smaller then summed calories of macronutrients')
                ->addViolation();
        }

        if(!$originalProduct){

        // Product being edited
        }else{

        }

//       FAJNA METODA DO SPRAWDZANIA KTORE POLE ENTITY BYLO UPDATOWANE
//        foreach (array_keys($originalProduct) as $key) {
//            $methodName = 'get' . ucfirst($key);
//            if (method_exists($value, $methodName) && $originalProduct[$key] != $value->{$methodName}()) {
//            }
//        }

    }
}
