<?php

namespace App\ApiResource\Controller;

use App\Repository\EnergyStationRepository;
use App\Service\EnergyStationsMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetEnergyStationsMap extends AbstractController
{
    public static string $operationName = 'get_energy_stations_map';

    public function __construct(
        private readonly EnergyStationRepository  $energyStationRepository,
        private readonly EnergyStationsMapService $energyStationsMapService,
        private readonly string                   $latitudeDefault,
        private readonly string                   $longitudeDefault,
        private readonly string                   $energyTypeUuidDefault,
        private readonly string                   $energyStationTypeDefault,
        private readonly string                   $radiusDefault
    ) {
    }

    public function __invoke(Request $request): array
    {
        $latitudeDefault = $request->query->get('latitude') ?? $this->latitudeDefault;
        $longitudeDefault = $request->query->get('longitude') ?? $this->longitudeDefault;
        $radiusDefault = $request->query->get('radius') ?? $this->radiusDefault;
        $energyTypeUuidDefault = $request->query->get('energy_type_uuid') ?? $this->energyTypeUuidDefault;
        $energyStationTypeDefault = $request->query->get('energy_station_type') ?? $this->energyStationTypeDefault;

        $filterCity = $request->query->get('filter_city') ?? null;
        $filterDepartment = $request->query->get('filter_department') ?? null;
        $filterEv = $request->query->get('filter_ev') ?? null;

        $energyStations = $this->energyStationRepository->getEnergyStationsMap(
            $longitudeDefault,
            $latitudeDefault,
            $energyStationTypeDefault,
            $energyTypeUuidDefault,
            $radiusDefault / 2,
            $filterCity,
            $filterDepartment
        );

        return $this->energyStationsMapService->invoke($energyStations, $energyTypeUuidDefault);
    }
}
