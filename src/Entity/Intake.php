<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IntakeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=IntakeRepository::class)
 */
class Intake
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $amountInGrams;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmountInGrams(): ?string
    {
        return $this->amountInGrams;
    }

    public function setAmountInGrams(string $amountInGrams): self
    {
        $this->amountInGrams = $amountInGrams;

        return $this;
    }
}
