<?php

namespace App\Entity\EntityId;

final class AddressId
{
    public function __construct(
        private readonly string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
