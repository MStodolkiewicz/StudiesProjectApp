<?php

namespace App\ApiPlatform;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Intake;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class IntakeExtension implements QueryCollectionExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if($resourceClass !== Intake::class || $this->security->isGranted('ROLE_ADMIN')){
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->where(sprintf("%s.user = :user", $rootAlias))
            ->setParameter('user', $this->security->getUser());
    }

}