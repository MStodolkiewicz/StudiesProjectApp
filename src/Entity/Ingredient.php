<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IngredientRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"ingredient:read"}},
 *     denormalizationContext={"groups"={"ingredient:write"}},
 *     attributes={
 *          "pagination_items_per_page"=1
 *     }
 *     )
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ingredient:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ingredient:read","ingredient:write"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="ingredients")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"ingredient:read"})
     */
    private $product;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $CreatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }
}
