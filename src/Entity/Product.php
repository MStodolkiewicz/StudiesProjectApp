<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ApiResource(
 *  normalizationContext={"groups"={"product:read"}},
 *  denormalizationContext={"groups"={"product:write"}},
 *  attributes={
 *       "pagination_items_per_page"=1
 *  }
 * )
 */

class Product
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=13)
     * @Groups({"product:read","product:write"})
     * @Assert\NotBlank()
     */
    private $barCodeNumbers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read","product:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read","product:write"})
     */
    private $brand;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read"})
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read"})
     */
    private $isDeleted = false;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read","product:write"})
     */
    private $proteins;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read","product:write"})
     */
    private $carbohydrates;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read","product:write"})
     */
    private $fat;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Groups({"product:read","product:write"})
     */
    private $kcal;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="product", orphanRemoval=true)
     * @Groups({"product:read"})
     */
    private $rates;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"product:read","product:write"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Ingredient::class, mappedBy="product", orphanRemoval=true)
     * @Groups({"product:read","product:write"})
     */
    private $ingredients;

    /**
     * @ORM\ManyToMany(targetEntity=Intake::class, inversedBy="products")
     * @Groups({"product:read"})
     */
    private $intakes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"product:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->intakes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function setRateAdmin():self
    {
        $this->user = new User();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @Groups({"product:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
