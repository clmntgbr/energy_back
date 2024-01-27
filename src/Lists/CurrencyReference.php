<?php

namespace App\Lists;

use App\Entity\Traits\ListTrait;

class CurrencyReference
{
    use ListTrait;

    public const EUR = 'eur';
    public const USD = 'usd';
}
