<?php

namespace App\Service;

use App\Entity\EnergyStation;
use App\Entity\EntityId\EnergyStationId;
use App\Message\CreateEnergyStationMessage;
use App\Message\UpdateEnergyStationMessage;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EnergyStationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface    $messageBus
    )
    {
    }

    public function getEnergyStationId($energyStationId): EnergyStationId
    {
        if (empty($energyStationId)) {
            throw new \Exception();
        }

        return new EnergyStationId($energyStationId);
    }

    public function setEnergyStationStatus(EnergyStation $energyStation, string $status): EnergyStation
    {
        $energyStation->setStatus($status);
        $this->em->persist($energyStation);
        $this->em->flush();

        return $energyStation;
    }

    public function createEnergyStationMessage(EnergyStationId $energyStationId, string $hash, array $datum)
    {
        $this->messageBus->dispatch(
            new CreateEnergyStationMessage(
                $energyStationId,
                $this->convert($datum['@attributes']['pop'] ?? ''),
                $hash,
                $this->convert($datum['@attributes']['cp'] ?? ''),
                $this->convert($datum['@attributes']['longitude'] ?? ''),
                $this->convert($datum['@attributes']['latitude'] ?? ''),
                $this->convert($datum['adresse'] ?? ''),
                $this->convert($datum['ville'] ?? ''),
                'FRANCE',
                $datum,
            ),
        );
    }

    private function convert($datum): string
    {
        if (is_array($datum)) {
            return implode(' ', $datum);
        }

        return $datum;
    }

    public function updateEnergyStationMessage(EnergyStationId $energyStationId, string $hash, array $datum)
    {
        $this->messageBus->dispatch(
            new UpdateEnergyStationMessage(
                $energyStationId,
                $this->convert($datum['@attributes']['pop'] ?? ''),
                $hash,
                $this->convert($datum['@attributes']['cp'] ?? ''),
                $this->convert($datum['@attributes']['longitude'] ?? ''),
                $this->convert($datum['@attributes']['latitude'] ?? ''),
                $this->convert($datum['adresse'] ?? ''),
                $this->convert($datum['ville'] ?? ''),
                'FRANCE',
                $datum,
            ),
        );
    }

    public function createEnergyStationServices(EnergyStation $energyStation, array $services)
    {
        if (!array_key_exists('service', $services['services'])) {
            return;
        }

        if (is_array($services['services']['service'])) {
            foreach ($services['services']['service'] as $item) {
                $energyStation->addService((new Slugify())->slugify($item, '_'), $item);
            }

            return;
        }

        if (is_string($services['services']['service'])) {
            $energyStation->addService((new Slugify())->slugify($services['services']['service'], '_'), $services['services']['service']);
        }
    }
}
