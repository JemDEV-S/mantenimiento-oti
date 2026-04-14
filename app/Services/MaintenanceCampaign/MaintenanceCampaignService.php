<?php

namespace App\Services\MaintenanceCampaign;

use App\Actions\MaintenanceCampaign\CreateCampaignAction;
use App\Actions\MaintenanceCampaign\UpdateCampaignAction;
use App\DTOs\MaintenanceCampaign\CreateMaintenanceCampaignDTO;
use App\DTOs\MaintenanceCampaign\UpdateMaintenanceCampaignDTO;
use App\Enums\CampaignAssetStatus;
use App\Exceptions\Asset\AssetException;
use App\Models\CampaignAsset;
use App\Models\MaintenanceCampaign;
use Illuminate\Pagination\LengthAwarePaginator;

class MaintenanceCampaignService
{
    public function __construct(
        private readonly CreateCampaignAction $createAction,
        private readonly UpdateCampaignAction $updateAction,
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

    public function delete(MaintenanceCampaign $campaign): void
    {
        $campaign->delete();
    }
}
