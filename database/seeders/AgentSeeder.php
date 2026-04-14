<?php

namespace Database\Seeders;

use App\Enums\AgentDeviceStatus;
use App\Enums\AgentSyncStatus;
use App\Enums\AgentSyncType;
use App\Models\AgentDevice;
use App\Models\AgentSync;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $asset = Asset::where('internal_code', 'EQ-OTI-001')->first();

        if (! $asset) {
            return;
        }

        $device = AgentDevice::updateOrCreate(
            ['asset_id' => $asset->id],
            [
                'hostname' => 'OTI-SOPORTE-01',
                'serial_number' => $asset->serial_number,
                'device_model' => $asset->model,
                'operating_system' => 'Windows 11 Pro',
                'agent_version' => '1.4.2',
                'last_ip' => '10.10.1.25',
                'last_heartbeat_at' => now()->subMinutes(3),
                'status' => AgentDeviceStatus::ACTIVO,
                'last_snapshot_json' => ['cpu' => 'Intel Core i7', 'ram' => '16 GB', 'disk_free' => '280 GB', 'antivirus' => 'Activo'],
            ]
        );

        $syncs = [
            ['agent_device_id' => $device->id, 'sync_type' => AgentSyncType::HEARTBEAT, 'payload_json' => ['status' => 'ok'], 'detected_changes_json' => null, 'status' => AgentSyncStatus::RECIBIDO, 'synced_at' => now()->subMinutes(3)],
            ['agent_device_id' => $device->id, 'sync_type' => AgentSyncType::SNAPSHOT, 'payload_json' => ['apps' => 48, 'services' => 92], 'detected_changes_json' => ['ram' => '16 GB', 'disk_free' => '280 GB'], 'status' => AgentSyncStatus::PROCESADO, 'synced_at' => now()->subDay()],
        ];

        foreach ($syncs as $sync) {
            AgentSync::updateOrCreate(
                ['agent_device_id' => $sync['agent_device_id'], 'sync_type' => $sync['sync_type'], 'synced_at' => $sync['synced_at']],
                $sync
            );
        }
    }
}
