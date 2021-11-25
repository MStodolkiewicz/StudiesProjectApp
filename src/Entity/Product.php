<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use App\Validator\ProductEdit;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('EDIT',object)",
 *          },
 *          "delete" = {
 *              "security"="is_granted('EDIT',object)",
 *              "security_message"="You don't have permission to delete this product."
 *          },
 *
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"
 *     },
 *  attributes={
 *       "pagination_items_per_page"=5
 *  }
 * )
 * @ApiFilter(SearchFilter::class, properties={"barCodeNumbers": "exact"})
 * @ProductEdit()
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="uuid",unique=true)
     * @ApiProperty(identifier=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=13)
     * @Groups({"product:read","product:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $barCodeNumbers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "product:write", "intake:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read", "product:write", "intake:read"})
     */
    private $brand;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"product:read", "intake:read","admin:write"})
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="datetime", length=180, nullable=true)
     * @Groups({"admin:read","admin:write"})
     */
    protected $deletedAt;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read", "product:write", "intake:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $proteins;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read","product:write", "intake:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $carbohydrates;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"product:read","product:write", "intake:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $fat;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Groups({"product:read","product:write", "intake:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $kcal;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="product", orphanRemoval=true)
     */
    private $rates;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"product:read","product:write", "intake:read"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Ingredient::class, mappedBy="product", orphanRemoval=true)
     * @Groups({"product:read","product:write", "intake:read"})
     */
    private $ingredients;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"admin:write"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Intake::class, mappedBy="product", orphanRemoval=true)
     */
    private $intakes;

    /**
     * @ORM\ManyToOne(targetEntity=SubCategory::class, inversedBy="products")
     * @Groups({"product:read","product:write", "intake:read"})
     */
    private $subCategory;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->intakes = new ArrayCollection();
        $this->uuid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
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

    public function setRateAdmin(): self
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

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
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
     * @Groups({"admin:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

//    /**
//     * @Groups({"product:read", "intake:read"})
//     */
//    public function getAvarageRate(): float
//    {
//
//        $avarage = 0;
//        foreach ($this->rates as $rate){
//            $avarage += $rate->getValue();
//        }
//
//        $avarage /= sizeof($this->rates);
//
//        return $avarage;
//    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

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
            $intake->setProduct($this);
        }

        return $this;
    }

    public function removeIntake(Intake $intake): self
    {
        if ($this->intakes->removeElement($intake)) {
            // set the owning side to null (unless already changed)
            if ($intake->getProduct() === $this) {
                $intake->setProduct(null);
            }
        }

        return $this;
    }

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }
 }
