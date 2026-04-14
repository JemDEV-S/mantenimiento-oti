<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMovement extends Model
{
    protected $fillable = [
        'asset_id',
        'movement_type',
        'origin_unit_id',
        'destination_unit_id',
        'from_employee_id',
        'to_employee_id',
        'movement_date',
        'reason',
        'document_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'movement_type' => MovementType::class,
        'movement_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function originUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'origin_unit_id');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'destination_unit_id');
    }

    public function fromEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
