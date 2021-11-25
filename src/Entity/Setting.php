<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=1024, nullable=false)
     */
    private $value;

    public function __construct(string $identifier, $value)
    {
        $this->setIdentifier($identifier);
        $this->setValue($value);
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): Setting
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return unserialize($this->value);
    }

    public function setValue(mixed $value): Setting
    {
        $this->value = serialize($value);

        return $this;
    }
}
