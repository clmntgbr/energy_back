<?php

namespace App\Service;

use App\Repository\EnergyStationRepository;

class EvUpdateCommandService
{
    public function __construct(
        private readonly string                  $evPath,
        private readonly string                  $evJsonName,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyStationService    $energyStationService
    )
    {
    }

    public function invoke(): void
    {

    }
}
