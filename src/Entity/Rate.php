<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RateRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"rate:read"}},
 *     denormalizationContext={"groups"={"rate:write"}}
 *     )
 */
class Rate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"rate:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"rate:read","rate:write"})
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rate:read","rate:write"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
