<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RateRepository;
use App\Validator\RateEdit;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RateRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('EDIT',object)",
 *          },
 *          "delete" = {
 *              "security"="is_granted('DELETE',object)",
 *          },
 *     },
 *     collectionOperations={
 *          "get",
 *          "post",
 *     },
 *     attributes={
 *          "pagination_items_per_page"=5
 *     }
 *     )
 * @RateEdit
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
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Range(min="1",max="5")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rate:read","rate:write"})
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rate:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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
