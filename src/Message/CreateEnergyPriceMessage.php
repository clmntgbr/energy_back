<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;
use App\Entity\EntityId\EnergyTypeId;

final readonly class CreateEnergyPriceMessage
{
    public function __construct(
        private EnergyStationId $energyStationId,
        private EnergyTypeId    $energyTypeId,
        private ?string         $date,
        private ?string         $value
    )
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }

    public function getEnergyTypeId(): EnergyTypeId
    {
        return $this->energyTypeId;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
