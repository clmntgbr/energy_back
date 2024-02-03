<?php

namespace App\Service;

use App\Entity\EnergyStation;

final class EnergyStationsMapService
{
    public function __construct(
        private array $lowEnergyPricesEnergyStationKey = ['keys' => [], 'energyPrice' => null, 'energyPriceId' => null],
    )
    {
    }

    /** @param EnergyStation[] $energyStations */
    public function invoke(array $energyStations, string $energyTypeUuid): array
    {
        foreach ($energyStations as $key => $energyStation) {
            if (!array_key_exists($energyTypeUuid, $energyStation->getLastEnergyPrices())) {
                continue;
            }

            $energyPrice = $energyStation->getLastEnergyPrices()[$energyTypeUuid];

            if (empty($this->lowEnergyPricesEnergyStationKey['keys'])) {
                $this->lowEnergyPricesEnergyStationKey = ['keys' => [$key], 'energyPrice' => $energyPrice['energyPriceValue']];
                continue;
            }

            if ($this->lowEnergyPricesEnergyStationKey['energyPrice'] > $energyPrice['energyPriceValue']) {
                $this->lowEnergyPricesEnergyStationKey = ['keys' => [$key], 'energyPrice' => $energyPrice['energyPriceValue']];
                continue;
            }

            if ($this->lowEnergyPricesEnergyStationKey['energyPrice'] == $energyPrice['energyPriceValue']) {
                $this->lowEnergyPricesEnergyStationKey['keys'][] = $key;
            }
        }

        foreach ($this->lowEnergyPricesEnergyStationKey['keys'] as $value) {
            $energyStations[$value]->setHasLowPrices(true);
        }

        return $energyStations;
    }
}
