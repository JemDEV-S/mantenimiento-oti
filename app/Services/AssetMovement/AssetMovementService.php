<?php

namespace App\Services\AssetMovement;

use App\Actions\AssetMovement\CreateMovementAction;
use App\DTOs\AssetMovement\CreateAssetMovementDTO;
use App\Models\Asset;
use App\Models\AssetMovement;
use Illuminate\Pagination\LengthAwarePaginator;

class AssetMovementService
{
    public function __construct(
        private readonly CreateMovementAction $createMovementAction,
    ) {}

    public function getPaginatedByAsset(Asset $asset, int $perPage = 10): LengthAwarePaginator
    {
        return $asset->movements()
            ->with('originUnit', 'destinationUnit', 'fromEmployee', 'toEmployee')
            ->paginate($perPage);
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return AssetMovement::with('asset', 'originUnit', 'destinationUnit', 'fromEmployee', 'toEmployee')
            ->when($filters['asset_id'] ?? null, fn ($q, $id) => $q->where('asset_id', $id))
            ->when($filters['type'] ?? null, fn ($q, $t) => $q->where('movement_type', $t))
            ->latest('movement_date')
            ->paginate(15)
            ->withQueryString();
    }

    public function create(CreateAssetMovementDTO $dto): AssetMovement
    {
        return $this->createMovementAction->execute($dto);
    }
}
