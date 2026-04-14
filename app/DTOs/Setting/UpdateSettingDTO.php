<?php

namespace App\DTOs\Setting;

readonly class UpdateSettingDTO
{
    public function __construct(
        public string $value,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
        );
    }
}
