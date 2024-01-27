<?php

namespace App\Entity\EntityId;

final class GasTypeId
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
