<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IntakeRepository;
use App\Validator\IntakeEdit;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Validator\IsMealTypeProper;


/**
 * @ORM\Entity(repositoryClass=IntakeRepository::class)
 *  @ApiResource(
 *     itemOperations={
 *          "get" = {
 *              "security"="is_granted('EDIT', object)",
 *          },
 *          "put" = {
 *              "security"="is_granted('EDIT',object)",
 *          },
 *          "delete" = {
 *              "security"="is_granted('EDIT',object)",
 *          },
 *
 *     },
 *     collectionOperations={
 *          "get",
 *           "post"
 *     },
 *     )
 * @IntakeEdit
 * @ApiFilter(DateFilter::class, properties={"createdAt"})
 * @ApiFilter(SearchFilter::class, properties={"mealType": "exact"})
 */
class Intake
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
     * @Groups({"intake:read","intake:write"})
     */
    private $uuid;


    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Groups({"intake:read","intake:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $amountInGrams;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="intakes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"intake:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"intake:read","intake:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @IsMealTypeProper()
     */
    private $mealType;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="intakes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"intake:read","intake:write"})
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $product;

    public function __construct()
    {
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
