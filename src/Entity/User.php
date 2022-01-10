<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Controller\User\ActivateAccountController;
use App\Controller\User\RegisterController;
use App\Dto\Request\User\ActivateAccountRequest;
use App\Dto\Request\User\RegisterUserRequest;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "security" = "object == user or is_granted('ROLE_ADMIN')"
 *          },
 *          "put"= {
 *              "security" = "object == user or is_granted('ROLE_ADMIN')",
 *          },
 *          "delete" = {
 *              "security" = "is_granted('ROLE_ADMIN')"
 *          },
 *     },
 *     collectionOperations={
 *          "get" = {
 *              "security" = "is_granted('ROLE_ADMIN')"
 *          },
 *          "post" = {
 *              "security" = "is_granted('ROLE_ADMIN')"
 *          },
 *          "register" = {
 *              "method" = "POST",
 *              "status" = 204,
 *              "path" = "/users/register",
 *              "controller" = RegisterController::class,
 *              "defaults" = {
 *                  "dto" = RegisterUserRequest::class,
 *               },
 *              "openapi_context" = {
 *                  "summary" = "Registers a new user",
 *                  "description" = "Registers a new user",
 *                  "requestBody" = {
 *                      "content" = {
 *                          "application/json" = {
 *                              "schema" = {
 *                                  "type" = "object",
 *                                  "properties" = {
 *                                      "username" = {
 *                                          "type" = "string",
 *                                          "example" = "John",
 *                                      },
 *                                      "email" = {
 *                                          "type" = "string",
 *                                          "example" = "johndoe@example.com",
 *                                      },
 *                                      "password" = {
 *                                          "type" = "string",
 *                                          "example" = "johnStrongPass123",
 *                                      },
 *                                  },
 *                              },
 *                          },
 *                      }
 *                },
 *                "responses" = {
 *                    204 = {
 *                        "description" = "The user is registered",
 *                    }
 *                }
 *            },
 *              "validate" = false,
 *              "read" = false,
 *              "deserialize" = false,
 *          },
 *        "activate" = {
 *            "method" = "POST",
 *            "status" = 204,
 *            "path" = "/users/activate",
 *            "controller" = ActivateAccountController::class,
 *            "defaults" = {
 *                "dto" = ActivateAccountRequest::class,
 *            },
 *            "openapi_context" = {
 *                "summary" = "Activates a user account",
 *                "description" = "Activates a user account",
 *                "requestBody" = {
 *                    "content" = {
 *                        "application/json" = {
 *                            "schema" = {
 *                                "type" = "object",
 *                                "properties" = {
 *                                    "token" = {
 *                                        "type" = "string",
 *                                        "example" = "TOKENSTRING",
 *                                    },
 *                                },
 *                            },
 *                        },
 *                    }
 *                },
 *                "responses" = {
 *                    204 = {
 *                        "description" = "The user is activated",
 *                    }
 *                }
 *            },
 *            "read" = false,
 *            "validate" = false,
 *            "deserialize" = false,
 *        },
 *     },
 * )
 *
 * @UniqueEntity(fields={"email", "username"}, message="There is already an account with this email or username")
 */


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

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
     * @ORM\Column(type="string", length=180, nullable=false, unique=true)
     * @Groups({"user:read","user:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read","user:write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:read","user:write"})
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Groups({"user:read","user:write","intake:read"})
     */
    private $username;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $height;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $weight;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $birthDate;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="user")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Intake::class, mappedBy="user", orphanRemoval=true)
     */

    private $intakes;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="user", orphanRemoval=true)
     */
    private $rates;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;


    /**
    * @ORM\Column(type="datetime", length=180, nullable=true)
    * @Groups({"admin:read","admin:write"})
    */
    protected $deletedAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:read","admin:write"})
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $desiredProtein;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $desiredFat;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $desiredWeight;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $desiredCarbohydrates;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"user:read","user:write"})
     */
    private $desiredCalories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->intakes = new ArrayCollection();
        $this->rates = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRolesAsString()
    {
        return implode(',', $this->roles);
    }

    public function setRolesAsString($string)
    {
        $this->setRoles(explode(',',$string));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

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
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
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
            $intake->setUser($this);
        }

        return $this;
    }

    public function removeIntake(Intake $intake): self
    {
        if ($this->intakes->removeElement($intake)) {
            // set the owning side to null (unless already changed)
            if ($intake->getUser() === $this) {
                $intake->setUser(null);
            }
        }

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
            $rate->setUser($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getUser() === $this) {
                $rate->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @Groups({"user:read"})
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
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getDesiredProtein(): ?float
    {
        return $this->desiredProtein;
    }

    public function setDesiredProtein(?float $desiredProtein): self
    {
        $this->desiredProtein = $desiredProtein;

        return $this;
    }

    public function getDesiredFat(): ?float
    {
        return $this->desiredFat;
    }

    public function setDesiredFat(?float $desiredFat): self
    {
        $this->desiredFat = $desiredFat;

        return $this;
    }

    public function getDesiredWeight(): ?float
    {
        return $this->desiredWeight;
    }

    public function setDesiredWeight(?float $desiredWeight): self
    {
        $this->desiredWeight = $desiredWeight;

        return $this;
    }

    public function getDesiredCarbohydrates(): ?float
    {
        return $this->desiredCarbohydrates;
    }

    public function setDesiredCarbohydrates(?float $desiredCarbohydrates): self
    {
        $this->desiredCarbohydrates = $desiredCarbohydrates;

        return $this;
    }

    public function getDesiredCalories(): ?float
    {
        return $this->desiredCalories;
    }

    public function setDesiredCalories(?float $desiredCalories): self
    {
        $this->desiredCalories = $desiredCalories;

        return $this;
    }

}
