<?php

namespace App\Service;

use App\Lists\EnergyStationReference;
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
        if (!FileSystemService::exist($this->evPath, $this->evJsonName)) {
            throw new \Exception('json ev prices dont exist.');
        }

        $file = fopen("$this->evPath/$this->evJsonName", 'r');

        $content = fread($file, filesize("$this->evPath/$this->evJsonName"));
        fclose($file);

        $data = json_decode($content, true);

        $energyStations = $this->energyStationRepository->findEnergyStationsById(EnergyStationReference::EV);

        foreach ($data as $datum) {

            if ($datum['id_station'] === null) {
                continue;
            }

            $energyStationId = $this->energyStationService->getEnergyStationId(preg_replace('/[^\p{L}\p{N}\s]/u', '', $datum['id_station']));

            if (!in_array(substr($datum['code_insee_commune'], 0, 2), ['94'])) {
                continue;
            }

            $hash = $this->energyStationService->getHash($datum);

            if (!array_key_exists($energyStationId->getId(), $energyStations)) {
                $this->energyStationService->createEnergyStationMessage($energyStationId, $hash, $this->hydrate($datum), EnergyStationReference::EV);
            }

            if (array_key_exists($energyStationId->getId(), $energyStations) && $energyStations[$energyStationId->getId()]['hash'] !== $hash) {
                $this->energyStationService->updateEnergyStationMessage($energyStationId, $hash, $this->hydrate($datum));
            }
        }
    }

    private function hydrate(array $datum)
    {
        $datum['name'] = $datum['n_amenageur'];
        $datum['@attributes'] = [
            'pop' => 'R',
            'cp' => $datum['code_insee_commune'] ?? '',
            'longitude' => $datum['xlongitude'] ?? '',
            'latitude' => $datum['ylatitude'] ?? '',
        ];
        $datum['adresse'] = $datum['ad_station'];
        $datum['ville'] = $datum['n_station'];

        return $datum;
    }

    public function getHash(array $datum): string
    {
        return hash('sha256', json_encode($datum));
    }
}
