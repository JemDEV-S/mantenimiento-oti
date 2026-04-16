<?php

namespace App\Services\AgentDevice;

use App\DTOs\AgentDevice\HeartbeatDTO;
use App\DTOs\AgentDevice\RegisterAgentDTO;
use App\DTOs\AgentSync\CreateAgentSyncDTO;
use App\Enums\AgentDeviceStatus;
use App\Enums\AgentSyncType;
use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Exceptions\AgentDevice\AgentDeviceException;
use App\Models\AgentDevice;
use App\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

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

    public function register(?Asset $asset, RegisterAgentDTO $dto): AgentDevice
    {
        if ($asset?->agentDevice()->exists()) {
            throw AgentDeviceException::assetAlreadyRegistered($asset->internal_code ?? $asset->name);
        }

        $plainToken = Str::random(60);
        $payload = array_merge($dto->toArray(), [
            'asset_id'            => $asset?->id,
            'status'             => AgentDeviceStatus::ACTIVO->value,
            'last_heartbeat_at'  => now(),
            'api_token'           => hash('sha256', $plainToken),
        ]);

        $device = AgentDevice::create($payload);
        $device->setAttribute('plain_api_token', $plainToken);

        return $device;
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

        if ($dto->asset_code || $dto->organizational_unit_id || $dto->responsible_employee_id) {
            $this->bindAsset(
                $device,
                $dto->asset_code,
                $dto->organizational_unit_id,
                $dto->responsible_employee_id,
                $dto->last_snapshot_json
            );
        }

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

    public function bindAsset(
        AgentDevice $device,
        ?string $assetCode,
        ?int $organizationalUnitId,
        ?int $responsibleEmployeeId,
        ?array $snapshot = null,
    ): AgentDevice {
        $asset = null;
        $assetCode = $assetCode ? trim($assetCode) : null;

        if ($assetCode) {
            $asset = Asset::query()
                ->where('internal_code', $assetCode)
                ->orWhere('patrimonial_code', $assetCode)
                ->first();
        }

        if (! $asset && $assetCode) {
            $asset = $this->createAssetFromAgentSnapshot(
                $assetCode,
                $device,
                $organizationalUnitId,
                $responsibleEmployeeId,
                $snapshot
            );
        }

        if (! $asset) {
            return $device->fresh();
        }

        if ($asset->agentDevice()->where('id', '!=', $device->id)->exists()) {
            throw AgentDeviceException::assetAlreadyRegistered($asset->internal_code ?? $asset->name);
        }

        $asset->update([
            'organizational_unit_id' => $organizationalUnitId ?? $asset->organizational_unit_id,
            'responsible_employee_id' => $responsibleEmployeeId ?? $asset->responsible_employee_id,
        ]);

        $device->update(['asset_id' => $asset->id]);

        return $device->fresh();
    }

    private function createAssetFromAgentSnapshot(
        string $assetCode,
        AgentDevice $device,
        ?int $organizationalUnitId,
        ?int $responsibleEmployeeId,
        ?array $snapshot,
    ): Asset {
        $hostname = data_get($snapshot, 'Device.Hostname')
            ?? data_get($snapshot, 'device.hostname')
            ?? $device->hostname;
        $manufacturer = data_get($snapshot, 'Device.Manufacturer')
            ?? data_get($snapshot, 'device.manufacturer');
        $model = data_get($snapshot, 'Device.Model')
            ?? data_get($snapshot, 'device.model')
            ?? $device->device_model;
        $serialNumber = data_get($snapshot, 'Device.SerialNumber')
            ?? data_get($snapshot, 'device.serialNumber')
            ?? data_get($snapshot, 'device.serial_number')
            ?? $device->serial_number;
        $operatingSystem = data_get($snapshot, 'Device.OperatingSystem')
            ?? data_get($snapshot, 'device.operatingSystem')
            ?? data_get($snapshot, 'device.operating_system')
            ?? $device->operating_system;

        return Asset::create([
            'internal_code' => mb_substr($assetCode, 0, 50),
            'patrimonial_code' => mb_substr($assetCode, 0, 50),
            'name' => $hostname ? "PC {$hostname}" : "PC {$assetCode}",
            'asset_type' => AssetType::COMPUTADORA->value,
            'brand' => $manufacturer ? mb_substr($manufacturer, 0, 100) : null,
            'model' => $model ? mb_substr($model, 0, 100) : null,
            'serial_number' => $serialNumber ? mb_substr($serialNumber, 0, 100) : null,
            'status' => AssetStatus::EN_USO->value,
            'condition' => AssetCondition::BUENO->value,
            'organizational_unit_id' => $organizationalUnitId,
            'responsible_employee_id' => $responsibleEmployeeId,
            'specs_json' => [
                'hostname' => $hostname,
                'cpu' => data_get($snapshot, 'Device.ProcessorName') ?? data_get($snapshot, 'device.processorName'),
                'ram_gb' => data_get($snapshot, 'Device.TotalMemoryGb') ?? data_get($snapshot, 'device.totalMemoryGb'),
                'operating_system' => $operatingSystem,
                'os_version' => data_get($snapshot, 'Device.OsVersion') ?? data_get($snapshot, 'device.osVersion'),
                'os_architecture' => data_get($snapshot, 'Device.OsArchitecture') ?? data_get($snapshot, 'device.osArchitecture'),
                'ip_addresses' => data_get($snapshot, 'Device.IpAddresses') ?? data_get($snapshot, 'device.ipAddresses'),
            ],
            'extra_json' => [
                'source' => 'agent',
                'agent_device_uuid' => $device->uuid,
                'collected_at_utc' => data_get($snapshot, 'CollectedAtUtc') ?? data_get($snapshot, 'collectedAtUtc'),
                'bios_version' => data_get($snapshot, 'Device.BiosVersion') ?? data_get($snapshot, 'device.biosVersion'),
            ],
            'notes' => 'Activo registrado automaticamente desde AgentSync.',
        ]);
    }
}
