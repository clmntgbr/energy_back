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
        private readonly string                   $radiusDefault
    )
    {
    }

    public function __invoke(Request $request): array
    {
        $latitude = $request->query->get('latitude') ?? $this->latitudeDefault;
        $longitude = $request->query->get('longitude') ?? $this->longitudeDefault;
        $radius = $request->query->get('radius') ?? $this->radiusDefault;
        $energyTypeUuid = $request->query->get('energy_type_uuid') ?? $this->energyTypeUuidDefault;
        $filterCity = $request->query->get('filter_city') ?? null;
        $filterDepartment = $request->query->get('filter_department') ?? null;

        $energyStations = $this->energyStationRepository->getEnergyStationsMap($longitude, $latitude, $energyTypeUuid, $radius / 2, $filterCity, $filterDepartment);
        return $this->energyStationsMapService->invoke($energyStations, $energyTypeUuid);
    }
}
