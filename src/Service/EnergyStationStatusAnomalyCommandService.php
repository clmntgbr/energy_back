<?php

namespace App\Service;

use App\Repository\EnergyStationRepository;

class EnergyStationStatusAnomalyCommandService
{
    public function __construct(
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly GooglePlaceService      $googlePlaceService
    )
    {
    }

    public function invoke(): void
    {
        $energyStations = $this->energyStationRepository->findEnergyStationsByPlaceIdNotNull();

        foreach ($energyStations as $energyStation) {
            $energyStationsAnomalies = $this->energyStationRepository->getEnergyStationGooglePlaceByPlaceId($energyStation);
            if (count($energyStationsAnomalies) > 0) {
                $this->googlePlaceService->createAnomalies(array_merge($energyStationsAnomalies, [$energyStation]));
            }
        }
    }
}
