<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final readonly class CreateGooglePlaceAnomalyMessage
{
    public function __construct(
        private EnergyStationId $energyStationId
    )
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }
}
