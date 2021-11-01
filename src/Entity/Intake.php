<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IntakeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IntakeRepository::class)
 *  @ApiResource(
 *     normalizationContext={"groups"={"intake:read"}},
 *     denormalizationContext={"groups"={"intake:write"}},
 *     attributes={
 *          "pagination_items_per_page"=1
 *     }
 *     )
 */
class Intake
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"intake:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Groups({"intake:read","intake:write"})
     */
    private $amountInGrams;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="intakes")
     * @Groups({"intake:read"})
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="intakes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"intake:read","intake:write"})
     */
    private $user;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addIntake($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeIntake($this);
        }

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
