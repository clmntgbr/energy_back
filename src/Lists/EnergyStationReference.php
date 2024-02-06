<?php

namespace App\Lists;

use App\Entity\Traits\ListTrait;

class EnergyStationReference
{
    use ListTrait;

    public const GAS = 'gas';
    public const EV = 'ev';
    public const MIX = 'mix';
}
