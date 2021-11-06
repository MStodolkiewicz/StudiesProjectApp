<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IntakeRepository;
use Carbon\Carbon;
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="intakes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"intake:read","intake:write"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"intake:read","intake:write"})
     */
    private $mealType;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="intakes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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
     * @Groups({"intake:read"})
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

    public function getMealType(): ?string
    {
        return $this->mealType;
    }

    public function setMealType(string $mealType): self
    {
        $this->mealType = $mealType;

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
}
