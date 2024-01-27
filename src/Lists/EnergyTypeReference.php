<?php

namespace App\Lists;

use App\Entity\Traits\ListTrait;

class EnergyTypeReference
{
    use ListTrait;

    public const GAZOLE = 'gazole';
    public const SP95 = 'sp95';
    public const E85 = 'e85';
    public const GPLC = 'gplc';
    public const E10 = 'e10';
    public const SP98 = 'sp98';
}
