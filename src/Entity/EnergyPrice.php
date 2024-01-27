<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\EnergyPriceRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: EnergyPriceRepository::class)]
#[ApiResource]
class EnergyPrice
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::INTEGER)]
    private int $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y h:i:s'])]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: Types::INTEGER)]
    private int $dateTimestamp;

    #[ORM\ManyToOne(targetEntity: EnergyStation::class, inversedBy: 'energyPrices')]
    #[ORM\JoinColumn(nullable: false)]
    private EnergyStation $energyStation;

    #[ORM\ManyToOne(targetEntity: EnergyType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private EnergyType $energyType;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDateTimestamp(): ?int
    {
        return $this->dateTimestamp;
    }

    public function setDateTimestamp(int $dateTimestamp): static
    {
        $this->dateTimestamp = $dateTimestamp;

        return $this;
    }

    public function getEnergyStation(): ?EnergyStation
    {
        return $this->energyStation;
    }

    public function setEnergyStation(?EnergyStation $energyStation): static
    {
        $this->energyStation = $energyStation;

        return $this;
    }

    public function getEnergyType(): ?EnergyType
    {
        return $this->energyType;
    }

    public function setEnergyType(?EnergyType $energyType): static
    {
        $this->energyType = $energyType;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}
