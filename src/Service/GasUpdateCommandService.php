<?php

namespace App\Service;

use App\Entity\EntityId\EnergyStationId;
use App\Entity\EntityId\EnergyTypeId;
use App\Lists\EnergyStationReference;
use App\Message\CreateEnergyPriceMessage;
use App\Repository\EnergyStationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class GasUpdateCommandService
{
    public function __construct(
        private readonly string                  $gasPath,
        private readonly string                  $gasJsonName,
        private readonly EnergyStationRepository $energyStationRepository,
        private readonly EnergyStationService    $energyStationService,
        private readonly MessageBusInterface     $messageBus,
    )
    {
    }

    public function invoke(): void
    {
        if (!FileSystemService::exist($this->gasPath, $this->gasJsonName)) {
            throw new \Exception('json gas prices dont exist.');
        }

        $file = fopen("$this->gasPath/$this->gasJsonName", 'r');

        $content = fread($file, filesize("$this->gasPath/$this->gasJsonName"));
        fclose($file);

        $data = json_decode($content, true);

        $energyStations = $this->energyStationRepository->findEnergyStationsById(EnergyStationReference::GAS);

        foreach ($data as $datum) {
            $energyStationId = $this->energyStationService->getEnergyStationId($datum['@attributes']['id']);

            if (!in_array(substr($energyStationId->getId(), 0, 2), ['94'])) {
                continue;
            }

            $hash = $this->energyStationService->getHash($datum);

            if (!array_key_exists($energyStationId->getId(), $energyStations)) {
                $this->energyStationService->createEnergyStationMessage($energyStationId, $hash, $datum, EnergyStationReference::GAS);
            }

            if (array_key_exists($energyStationId->getId(), $energyStations) && $energyStations[$energyStationId->getId()]['hash'] !== $hash) {
                $this->energyStationService->updateEnergyStationMessage($energyStationId, $hash, $datum);
            }

            $this->createEnergyPricesMessage($energyStationId, $datum);
        }
    }

    private function createEnergyPricesMessage(EnergyStationId $energyStationId, array $datum)
    {
        foreach ($datum['prix'] ?? [] as $item) {
            $energyTypeDatum = $this->getEnergyTypeId($datum, $item);
            $this->messageBus->dispatch(
                new CreateEnergyPriceMessage(
                    $energyStationId,
                    $energyTypeDatum['energyTypeId'],
                    $energyTypeDatum['date'],
                    $energyTypeDatum['value']
                )
            );
        }
    }

    private function getEnergyTypeId(array $element, array $item): array
    {
        $EnergyTypeId = new EnergyTypeId($item['@attributes']['id'] ?? 0);
        $date = $item['@attributes']['maj'] ?? null;
        $value = $item['@attributes']['valeur'] ?? null;

        if (1 == count($element['prix'])) {
            $EnergyTypeId = new EnergyTypeId($item['id'] ?? 0);
            $date = $item['maj'] ?? null;
            $value = $item['valeur'] ?? null;
        }

        return ['energyTypeId' => $EnergyTypeId, 'date' => $date, 'value' => $value];
    }
}
