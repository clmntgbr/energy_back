<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\EvInformationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EvInformationRepository::class)]
#[ApiResource]
class EvInformation
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $numberRechargePoint;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $maximumPower;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $typeOfCharging;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $chargingAccess;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $accessibility;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations;

    public function getNumberRechargePoint(): ?string
    {
        return $this->numberRechargePoint;
    }

    public function setNumberRechargePoint(?string $numberRechargePoint): static
    {
        $this->numberRechargePoint = $numberRechargePoint;

        return $this;
    }

    public function getMaximumPower(): ?string
    {
        return $this->maximumPower;
    }

    public function setMaximumPower(?string $maximumPower): static
    {
        $this->maximumPower = $maximumPower;

        return $this;
    }

    public function getTypeOfCharging(): ?string
    {
        return $this->typeOfCharging;
    }

    public function setTypeOfCharging(?string $typeOfCharging): static
    {
        $this->typeOfCharging = $typeOfCharging;

        return $this;
    }

    public function getChargingAccess(): ?string
    {
        return $this->chargingAccess;
    }

    public function setChargingAccess(?string $chargingAccess): static
    {
        $this->chargingAccess = $chargingAccess;

        return $this;
    }

    public function getAccessibility(): ?string
    {
        return $this->accessibility;
    }

    public function setAccessibility(?string $accessibility): static
    {
        $this->accessibility = $accessibility;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }
}
