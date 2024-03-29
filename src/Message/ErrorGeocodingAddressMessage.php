<?php

namespace App\Message;

use App\Entity\EntityId\AddressId;
use App\Entity\EntityId\EnergyStationId;

final readonly class ErrorGeocodingAddressMessage
{
    public function __construct(
        private AddressId       $addressId,
        private EnergyStationId $energyStationId
    )
    {
    }

    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    public function getEnergyStationId(): EnergyStationId
    {
        return $this->energyStationId;
    }
}
