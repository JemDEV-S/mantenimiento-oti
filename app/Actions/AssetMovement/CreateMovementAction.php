<?php

namespace App\Actions\AssetMovement;

use App\DTOs\AssetMovement\CreateAssetMovementDTO;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use Illuminate\Support\Facades\DB;

class CreateMovementAction
{
    public function execute(CreateAssetMovementDTO $dto): AssetMovement
    {
        return DB::transaction(function () use ($dto) {
            $movement = AssetMovement::create($dto->toArray());

            // Actualizar unidad y responsable del activo según el tipo de movimiento
            $asset = Asset::findOrFail($dto->asset_id);
            $updates = [];

            if ($dto->destination_unit_id) {
                $updates['organizational_unit_id'] = $dto->destination_unit_id;
            }

            if ($dto->to_employee_id) {
                $updates['responsible_employee_id'] = $dto->to_employee_id;
                $updates['status'] = AssetStatus::EN_USO->value;
            }

            if (! empty($updates)) {
                $asset->update($updates);
            }

            return $movement;
        });
    }
}
