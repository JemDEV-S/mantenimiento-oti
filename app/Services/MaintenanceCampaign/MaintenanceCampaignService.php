<?php

namespace App\Services\MaintenanceCampaign;

use App\Actions\MaintenanceCase\CreateCaseAction;
use App\Actions\MaintenanceCampaign\CreateCampaignAction;
use App\Actions\MaintenanceCampaign\UpdateCampaignAction;
use App\DTOs\MaintenanceCase\CreateMaintenanceCaseDTO;
use App\DTOs\MaintenanceCampaign\CreateMaintenanceCampaignDTO;
use App\DTOs\MaintenanceCampaign\UpdateMaintenanceCampaignDTO;
use App\Enums\CampaignAssetStatus;
use App\Enums\MaintenanceCaseStatus;
use App\Exceptions\Asset\AssetException;
use App\Models\Asset;
use App\Models\CampaignAsset;
use App\Models\MaintenanceCampaign;
use Illuminate\Pagination\LengthAwarePaginator;

class MaintenanceCampaignService
{
    public function __construct(
        private readonly CreateCampaignAction $createAction,
        private readonly UpdateCampaignAction $updateAction,
        private readonly CreateCaseAction     $createCaseAction,
    ) {}

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return MaintenanceCampaign::with('coordinator')
            ->search($filters['search'] ?? null)
            ->byStatus($filters['status'] ?? null)
            ->latest('start_date')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(CreateMaintenanceCampaignDTO $dto): MaintenanceCampaign
    {
        return $this->createAction->execute($dto);
    }

    public function update(MaintenanceCampaign $campaign, UpdateMaintenanceCampaignDTO $dto): MaintenanceCampaign
    {
        return $this->updateAction->execute($campaign, $dto);
    }

    public function addAsset(MaintenanceCampaign $campaign, int $assetId, array $data = []): CampaignAsset
    {
        if ($campaign->campaignAssets()->where('asset_id', $assetId)->exists()) {
            throw AssetException::alreadyAssigned((string) $assetId);
        }

        return $campaign->campaignAssets()->create([
            'asset_id'               => $assetId,
            'assigned_technician_id' => $data['assigned_technician_id'] ?? null,
            'scheduled_date'         => $data['scheduled_date'] ?? null,
            'status'                 => CampaignAssetStatus::PENDIENTE->value,
            'notes'                  => $data['notes'] ?? null,
        ]);
    }

    public function removeAsset(MaintenanceCampaign $campaign, int $assetId): void
    {
        $campaign->campaignAssets()->where('asset_id', $assetId)->delete();
    }

    public function bulkAddByUnit(MaintenanceCampaign $campaign, int $unitId, array $data = []): int
    {
        $existingIds = $campaign->campaignAssets()->pluck('asset_id');
        $assets = Asset::where('organizational_unit_id', $unitId)
            ->whereNotIn('id', $existingIds)
            ->get();

        foreach ($assets as $asset) {
            $campaign->campaignAssets()->create([
                'asset_id'               => $asset->id,
                'assigned_technician_id' => $data['assigned_technician_id'] ?: null,
                'scheduled_date'         => $data['scheduled_date'] ?? null,
                'status'                 => CampaignAssetStatus::PENDIENTE->value,
                'notes'                  => $data['notes'] ?? null,
            ]);
        }

        return $assets->count();
    }

    public function bulkCreateCases(MaintenanceCampaign $campaign, array $data): int
    {
        $campaignAssets = $campaign->campaignAssets()
            ->whereNull('maintenance_case_id')
            ->get();

        $count = 0;
        foreach ($campaignAssets as $ca) {
            $case = $this->createCaseAction->execute(
                CreateMaintenanceCaseDTO::fromArray([
                    'asset_id'               => $ca->asset_id,
                    'campaign_id'            => $campaign->id,
                    'assigned_technician_id' => $data['assigned_technician_id'] ?: null,
                    'maintenance_type'       => $data['maintenance_type'],
                    'priority'               => $data['priority'] ?? 'media',
                    'status'                 => MaintenanceCaseStatus::PENDIENTE->value,
                    'problem_description'    => $data['problem_description'] ?: 'Mantenimiento programado — campaña ' . $campaign->name,
                    'created_by'             => $data['created_by'],
                ])
            );

            $ca->update([
                'maintenance_case_id'    => $case->id,
                'assigned_technician_id' => $data['assigned_technician_id'] ?: $ca->assigned_technician_id,
                'status'                 => CampaignAssetStatus::PROGRAMADO->value,
            ]);

            $count++;
        }

        return $count;
    }

    public function delete(MaintenanceCampaign $campaign): void
    {
        $campaign->delete();
    }
}
