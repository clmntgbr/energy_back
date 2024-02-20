<?php

namespace App\Lists;

use App\Entity\Traits\ListTrait;

class EnergyStationReference
{
    use ListTrait;

    public const GAS = 'GAS';
    public const EV = 'EV';
    public const MIX = 'MIX';
}
