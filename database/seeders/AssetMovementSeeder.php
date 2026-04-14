<?php

namespace Database\Seeders;

use App\Enums\MovementType;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetMovementSeeder extends Seeder
{
    public function run(): void
    {
        $assets = Asset::query()->get()->keyBy('internal_code');
        $units = OrganizationalUnit::query()->get()->keyBy('code');
        $employees = Employee::query()->get()->keyBy('dni');
        $createdBy = User::where('email', 'superadmin@mdsj.local')->value('id')
            ?? User::where('email', 'carlos.ramirez@mdsj.local')->value('id');

        $movements = [
            ['asset_id' => $assets['EQ-OTI-001']->id, 'movement_type' => MovementType::ASIGNACION, 'origin_unit_id' => $units['ALM']->id, 'destination_unit_id' => $units['OTI']->id, 'from_employee_id' => $employees['70789012']->id, 'to_employee_id' => $employees['70234567']->id, 'movement_date' => '2025-07-16', 'reason' => 'Asignacion inicial', 'document_number' => 'MOV-2025-001', 'notes' => 'Entrega para atencion de incidencias.', 'created_by' => $createdBy],
            ['asset_id' => $assets['EQ-TES-002']->id, 'movement_type' => MovementType::TRASLADO, 'origin_unit_id' => $units['ADM']->id, 'destination_unit_id' => $units['TES']->id, 'from_employee_id' => $employees['70567890']->id, 'to_employee_id' => $employees['70456789']->id, 'movement_date' => '2025-02-03', 'reason' => 'Reordenamiento interno', 'document_number' => 'MOV-2025-014', 'created_by' => $createdBy],
            ['asset_id' => $assets['EQ-ALM-006']->id, 'movement_type' => MovementType::INGRESO, 'origin_unit_id' => null, 'destination_unit_id' => $units['ALM']->id, 'from_employee_id' => null, 'to_employee_id' => $employees['70789012']->id, 'movement_date' => '2022-06-18', 'reason' => 'Alta patrimonial', 'document_number' => 'ING-2022-009', 'created_by' => $createdBy],
        ];

        foreach ($movements as $movement) {
            AssetMovement::updateOrCreate(
                ['asset_id' => $movement['asset_id'], 'movement_date' => $movement['movement_date'], 'document_number' => $movement['document_number']],
                $movement
            );
        }
    }
}
