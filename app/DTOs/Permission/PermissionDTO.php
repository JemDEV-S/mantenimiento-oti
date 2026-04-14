<?php

namespace App\DTOs\Permission;

readonly class PermissionDTO
{
    public function __construct(
        public string $name,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
        );
    }
}
