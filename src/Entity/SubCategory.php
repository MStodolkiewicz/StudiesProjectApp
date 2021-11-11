<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SubCategoryRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubCategoryRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to change this resource!"
 *          },
 *          "delete" = {
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to change this resource!"
 *          },
 *
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="You are not allowed to change this resource!"
 *           }
 *     },
 *     attributes={
 *          "pagination_items_per_page"=1
 *     },
 *     )
 */

class SubCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"subcategory:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"subcategory:read","subcategory:write", "category:read","category:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="subCategories")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"subcategory:read","subcategory:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $category;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="subCategory")
     */
    private $products;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
        $this->products = new ArrayCollection();
    }

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $product->setSubCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSubCategory() === $this) {
                $product->setSubCategory(null);
            }
        }

        return $this;
    }
}
