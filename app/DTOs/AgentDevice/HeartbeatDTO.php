<?php

namespace App\DTOs\AgentDevice;

readonly class HeartbeatDTO
{
    public function __construct(
        public string  $agent_version,
        public ?string $last_ip,
        public ?array  $last_snapshot_json,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            agent_version:      $data['agent_version'],
            last_ip:            $data['last_ip'] ?? null,
            last_snapshot_json: $data['last_snapshot_json'] ?? null,
        );
    }
}
