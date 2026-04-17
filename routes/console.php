<?php

use App\Enums\AgentSyncType;
use App\Models\AgentSync;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'agents:purge-sync-history {--heartbeat-days=30 : Dias de retencion para heartbeats} {--snapshot-days=90 : Dias de retencion para snapshots}',
    function (): int {
        $heartbeatDays = max(1, (int) $this->option('heartbeat-days'));
        $snapshotDays = max(1, (int) $this->option('snapshot-days'));

        $heartbeatDeleted = AgentSync::query()
            ->where('sync_type', AgentSyncType::HEARTBEAT->value)
            ->where('synced_at', '<', now()->subDays($heartbeatDays))
            ->delete();

        $snapshotDeleted = AgentSync::query()
            ->where('sync_type', AgentSyncType::SNAPSHOT->value)
            ->where('synced_at', '<', now()->subDays($snapshotDays))
            ->delete();

        $this->info("Heartbeats eliminados: {$heartbeatDeleted}");
        $this->info("Snapshots eliminados: {$snapshotDeleted}");

        return self::SUCCESS;
    }
)->purpose('Elimina heartbeats y snapshots antiguos de agent_syncs.');

Schedule::command('agents:purge-sync-history')->daily();
