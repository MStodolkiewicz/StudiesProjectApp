<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ApiResource
 */

class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $barCodeNumbers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $proteins;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $carbohydrates;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $fat;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $kcal;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="product", orphanRemoval=true)
     */
    private $rates;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Ingredient::class, mappedBy="product", orphanRemoval=true)
     */
    private $ingredients;

    /**
     * @ORM\ManyToMany(targetEntity=Intake::class, inversedBy="products")
     */
    private $intakes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->intakes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBarCodeNumbers(): ?string
    {
        return $this->barCodeNumbers;
    }

    public function setBarCodeNumbers(string $barCodeNumbers): self
    {
        $this->barCodeNumbers = $barCodeNumbers;

        return $this;
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getProteins(): ?string
    {
        return $this->proteins;
    }

    public function setProteins(string $proteins): self
    {
        $this->proteins = $proteins;

        return $this;
    }

    public function getCarbohydrates(): ?string
    {
        return $this->carbohydrates;
    }

    public function setCarbohydrates(string $carbohydrates): self
    {
        $this->carbohydrates = $carbohydrates;

        return $this;
    }

    public function getFat(): ?string
    {
        return $this->fat;
    }

    public function setFat(string $fat): self
    {
        $this->fat = $fat;

        return $this;
    }

    public function getKcal(): ?string
    {
        return $this->kcal;
    }

    public function setKcal(string $kcal): self
    {
        $this->kcal = $kcal;

        return $this;
    }

    /**
     * @return Collection|Rate[]
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rate $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $this->rates[] = $rate;
            $rate->setProduct($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getProduct() === $this) {
                $rate->setProduct(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setProduct($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getProduct() === $this) {
                $ingredient->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Intake[]
     */
    public function getIntakes(): Collection
    {
        return $this->intakes;
    }

    public function addIntake(Intake $intake): self
    {
        if (!$this->intakes->contains($intake)) {
            $this->intakes[] = $intake;
        }

        return $this;
    }

    public function removeIntake(Intake $intake): self
    {
        $this->intakes->removeElement($intake);

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