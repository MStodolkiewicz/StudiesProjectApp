<?php

namespace App\DataPersister;

//   DODAC automatycznie użytkownika

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProductDataPersister implements \ApiPlatform\Core\DataPersister\DataPersisterInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager,Security $security)
    {

        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof Product;
    }

    /**
     * @inheritDoc
     */
    public function persist($data)
    {
        /** @var Product $data */
        $data->setUser($this->security->getUser());

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}