<?php

namespace App\Services\AgentDevice;

use App\DTOs\AgentDevice\HeartbeatDTO;
use App\DTOs\AgentDevice\RegisterAgentDTO;
use App\DTOs\AgentSync\CreateAgentSyncDTO;
use App\Enums\AgentDeviceStatus;
use App\Enums\AgentSyncType;
use App\Exceptions\AgentDevice\AgentDeviceException;
use App\Models\AgentDevice;
use App\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;

class AgentDeviceService
{
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return AgentDevice::with('asset')
            ->byStatus($filters['status'] ?? null)
            ->latest('last_heartbeat_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function register(Asset $asset, RegisterAgentDTO $dto): AgentDevice
    {
        if ($asset->agentDevice()->exists()) {
            throw AgentDeviceException::assetAlreadyRegistered($asset->internal_code ?? $asset->name);
        }

        return $asset->agentDevice()->create(array_merge($dto->toArray(), [
            'status'             => AgentDeviceStatus::ACTIVO->value,
            'last_heartbeat_at'  => now(),
        ]));
    }

    public function heartbeat(AgentDevice $device, HeartbeatDTO $dto): AgentDevice
    {
        $device->update([
            'agent_version'      => $dto->agent_version,
            'last_ip'            => $dto->last_ip,
            'last_heartbeat_at'  => now(),
            'status'             => AgentDeviceStatus::ACTIVO->value,
            'last_snapshot_json' => $dto->last_snapshot_json ?? $device->last_snapshot_json,
        ]);

        $device->syncs()->create([
            'sync_type'  => AgentSyncType::HEARTBEAT->value,
            'status'     => \App\Enums\AgentSyncStatus::PROCESADO->value,
            'synced_at'  => now(),
        ]);

        return $device->fresh();
    }

    public function receiveSync(AgentDevice $device, CreateAgentSyncDTO $dto): \App\Models\AgentSync
    {
        $sync = $device->syncs()->create($dto->toArray());

        if ($dto->sync_type === AgentSyncType::SNAPSHOT && $dto->payload_json) {
            $device->update(['last_snapshot_json' => $dto->payload_json]);
        }

        $sync->update(['status' => \App\Enums\AgentSyncStatus::PROCESADO->value]);

        return $sync->fresh();
    }

    public function findByToken(string $token): AgentDevice
    {
        $device = AgentDevice::where('api_token', hash('sha256', $token))->first();

        if (! $device) {
            throw AgentDeviceException::unauthorized();
        }

        return $device;
    }
}
