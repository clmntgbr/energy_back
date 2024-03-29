<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final readonly class UpdateEnergyStationClosedMessage
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
