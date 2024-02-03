<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final class CreateGooglePlaceDetailsMessage
{
    public function __construct(private readonly EnergyStationId $energyStationId)
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }
}
