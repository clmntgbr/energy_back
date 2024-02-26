<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\EvRechargePointRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EvRechargePointRepository::class)]
#[ApiResource]
class EvRechargePoint
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $typeOfCharging;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $powerKW;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $level;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $isFastChargeCapable;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $quantity;

    #[ORM\ManyToOne(targetEntity: EvInformation::class, inversedBy: 'evRechargePoints')]
    private EvInformation $evinformation;

    public function getTypeOfCharging(): ?string
    {
        return $this->typeOfCharging;
    }

    public function setTypeOfCharging(?string $typeOfCharging): static
    {
        $this->typeOfCharging = $typeOfCharging;

        return $this;
    }

    public function getPowerKW(): ?string
    {
        return $this->powerKW;
    }

    public function setPowerKW(?string $powerKW): static
    {
        $this->powerKW = $powerKW;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getIsFastChargeCapable(): ?string
    {
        return $this->isFastChargeCapable;
    }

    public function setIsFastChargeCapable(?string $isFastChargeCapable): static
    {
        $this->isFastChargeCapable = $isFastChargeCapable;

        return $this;
    }

    public function getEvinformation(): ?EvInformation
    {
        return $this->evinformation;
    }

    public function setEvinformation(?EvInformation $evinformation): static
    {
        $this->evinformation = $evinformation;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
