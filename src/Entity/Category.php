<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoryRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to change this resource!"
 *          },
 *          "delete" = {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to delete this resource!"
 *          },
 *
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to add this resource!"
 *           }
 *     },
 *     )
 */
class Category
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
     * @Groups({"category:read", "category:write", "subcategory:read", "intake:read", "product:read"})
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"category:read", "category:write", "subcategory:read", "intake:read", "product:read"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=SubCategory::class, mappedBy="category", orphanRemoval=true, cascade={"persist"})
     * @Groups({"category:read","category:write"})
     * @Assert\Valid()
     */
    private $subCategories;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subCategories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SubCategory[]
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): self
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

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

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
