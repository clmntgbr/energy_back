<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final class CreateEnergyStationMessage
{
    /**
     * @param array<mixed> $element
     */
    public function __construct(
        private readonly EnergyStationId $energyStationId,
        private readonly string          $pop,
        private readonly string $type,
        private readonly string          $hash,
        private readonly string          $cp,
        private readonly ?string         $longitude,
        private readonly ?string         $latitude,
        private readonly string          $street,
        private readonly string          $city,
        private readonly string          $country,
        private readonly array           $element
    )
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
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

    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return array<mixed>
     */
    public function getElement()
    {
        return $this->element;
    }
}
