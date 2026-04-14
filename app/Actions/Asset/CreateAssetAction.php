<?php

namespace App\Actions\Asset;

use App\DTOs\Asset\CreateAssetDTO;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class CreateAssetAction
{
    public function execute(CreateAssetDTO $dto): Asset
    {
        return DB::transaction(function () use ($dto) {
            return Asset::create($dto->toArray());
        });
    }
}
