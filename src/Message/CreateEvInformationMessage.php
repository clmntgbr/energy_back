<?php

namespace App\Message;

use App\Entity\EntityId\EnergyStationId;

final readonly class CreateEvInformationMessage
{
    public function __construct(
        private EnergyStationId $energyStationId,
        private array           $element
    )
    {
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }

    public function getElement(): array
    {
        return $this->element;
    }
}
