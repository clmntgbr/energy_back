<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final readonly class CreateEnergyStationMessage
{
    public function __construct(
        private EnergyStationId $energyStationId,
        private ?string         $name,
        private string          $pop,
        private string          $type,
        private string          $hash,
        private string          $cp,
        private ?string         $longitude,
        private ?string         $latitude,
        private string          $street,
        private string          $city,
        private array           $element
    )
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPop(): string
    {
        return $this->pop;
    }

    public function getCp(): string
    {
        return $this->cp;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getElement(): array
    {
        return $this->element;
    }
}
