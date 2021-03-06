<?php

namespace App\DataPersister;

use App\Entity\Intake;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class IntakeDataPersister implements \ApiPlatform\Core\DataPersister\DataPersisterInterface
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
        return $data instanceof Intake;
    }

    /**
     * @inheritDoc
     */
    public function persist($data)
    {

        /** @var Intake $data */
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