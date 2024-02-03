<?php

namespace App\Service;

use App\Entity\EnergyStation;
use App\Entity\EntityId\EnergyStationId;
use App\Lists\EnergyStationStatusReference;
use App\Message\UpdateEnergyStationClosedMessage;
use App\Repository\EnergyStationRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class EnergyStationClosedCommandService
{
    public function __construct(
        private EnergyStationRepository      $energyStationRepository,
        private readonly MessageBusInterface $messageBus
    )
    {
    }

    public function invoke()
    {
        $energyStations = $this->energyStationRepository->findEnergyStationsExceptByStatus(EnergyStationStatusReference::CLOSED);

        foreach ($energyStations as $energyStation) {
            if (null === $energyStation->getLastEnergyPrices()) {
                return $this->sendTo($energyStation);
            }
        }
    }

    private function sendTo(EnergyStation $energyStation)
    {
        return $this->messageBus->dispatch(new UpdateEnergyStationClosedMessage(new EnergyStationId($energyStation->getEnergyStationId())));
    }
}