<?php

namespace App\Entity\EntityId;

final class EnergyTypeId
{
    public function __construct(
        private readonly int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
