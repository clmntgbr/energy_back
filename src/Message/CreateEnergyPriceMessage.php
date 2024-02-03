<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;
use App\Entity\EntityId\EnergyTypeId;

final class CreateEnergyPriceMessage
{
    public function __construct(
        private readonly EnergyStationId $energyStationId,
        private readonly EnergyTypeId    $energyTypeId,
        private readonly ?string         $date,
        private readonly ?string         $value
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
