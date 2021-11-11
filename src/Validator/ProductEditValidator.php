<?php

namespace App\Validator;

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

        $originalProduct = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (($originalProduct['isVerified'] || $originalProduct['isDeleted'])) {

            $this->context->buildViolation($constraint->message)
                ->addViolation();

        }
//       FAJNA METODA DO SPRAWDZANIA KTORE POLE ENTITY BYLO UPDATOWANE
//        foreach (array_keys($originalProduct) as $key) {
//            $methodName = 'get' . ucfirst($key);
//            if (method_exists($value, $methodName) && $originalProduct[$key] != $value->{$methodName}()) {
//            }
//        }

        if (!$value instanceof Product) {
            throw new \LogicException(sprintf("Object passed to this validator must be of type %s", Product::class));
        }
    }


}
