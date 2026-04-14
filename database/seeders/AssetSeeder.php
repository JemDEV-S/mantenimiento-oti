<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $units = OrganizationalUnit::query()->get()->keyBy('code');
        $employees = Employee::query()->get()->keyBy('dni');

        $assets = [
            ['internal_code' => 'EQ-OTI-001', 'patrimonial_code' => 'PAT-2026-0001', 'name' => 'Laptop Dell Latitude 5440', 'asset_type' => AssetType::LAPTOP, 'brand' => 'Dell', 'model' => 'Latitude 5440', 'serial_number' => 'DLL5440A001', 'status' => AssetStatus::EN_USO, 'condition' => AssetCondition::BUENO, 'organizational_unit_id' => $units['OTI']->id, 'responsible_employee_id' => $employees['70234567']->id, 'purchase_date' => '2025-07-15', 'reference_value' => 4200.00, 'specs_json' => ['cpu' => 'Core i7', 'ram' => '16 GB', 'disk' => '512 GB SSD'], 'extra_json' => ['warranty_until' => '2027-07-15'], 'notes' => 'Equipo principal de soporte.'],
            ['internal_code' => 'EQ-TES-002', 'patrimonial_code' => 'PAT-2026-0002', 'name' => 'PC Tesoreria 01', 'asset_type' => AssetType::COMPUTADORA, 'brand' => 'Lenovo', 'model' => 'ThinkCentre Neo', 'serial_number' => 'LNVNEO22001', 'status' => AssetStatus::EN_USO, 'condition' => AssetCondition::BUENO, 'organizational_unit_id' => $units['TES']->id, 'responsible_employee_id' => $employees['70456789']->id, 'purchase_date' => '2024-03-20', 'reference_value' => 3500.00, 'specs_json' => ['cpu' => 'Core i5', 'ram' => '8 GB', 'disk' => '256 GB SSD']],
            ['internal_code' => 'EQ-ADM-003', 'patrimonial_code' => 'PAT-2026-0003', 'name' => 'Impresora Multifuncional Administracion', 'asset_type' => AssetType::IMPRESORA, 'brand' => 'Epson', 'model' => 'L6270', 'serial_number' => 'EPSL6270003', 'status' => AssetStatus::ACTIVO, 'condition' => AssetCondition::REGULAR, 'organizational_unit_id' => $units['ADM']->id, 'responsible_employee_id' => $employees['70567890']->id, 'purchase_date' => '2023-11-10', 'reference_value' => 1800.00],
            ['internal_code' => 'EQ-OBR-004', 'patrimonial_code' => 'PAT-2026-0004', 'name' => 'Proyector Sala de Obras', 'asset_type' => AssetType::PROYECTOR, 'brand' => 'BenQ', 'model' => 'MH560', 'serial_number' => 'BNQMH560004', 'status' => AssetStatus::EN_ALMACEN, 'condition' => AssetCondition::BUENO, 'organizational_unit_id' => $units['OBR']->id, 'responsible_employee_id' => $employees['70678901']->id, 'purchase_date' => '2024-08-22', 'reference_value' => 2400.00],
            ['internal_code' => 'EQ-OTI-005', 'patrimonial_code' => 'PAT-2026-0005', 'name' => 'Switch Core 24 Puertos', 'asset_type' => AssetType::SWITCH, 'brand' => 'Cisco', 'model' => 'CBS250-24T-4G', 'serial_number' => 'CSC24PORT005', 'status' => AssetStatus::ACTIVO, 'condition' => AssetCondition::BUENO, 'organizational_unit_id' => $units['OTI']->id, 'responsible_employee_id' => $employees['70123456']->id, 'purchase_date' => '2025-01-12', 'reference_value' => 3100.00],
            ['internal_code' => 'EQ-ALM-006', 'patrimonial_code' => 'PAT-2026-0006', 'name' => 'UPS Rack Principal', 'asset_type' => AssetType::UPS, 'brand' => 'APC', 'model' => 'Smart-UPS 2200', 'serial_number' => 'APCUPS2200006', 'status' => AssetStatus::EN_REPARACION, 'condition' => AssetCondition::REGULAR, 'organizational_unit_id' => $units['ALM']->id, 'responsible_employee_id' => $employees['70789012']->id, 'purchase_date' => '2022-06-18', 'reference_value' => 5200.00, 'notes' => 'Pendiente de cambio de baterias.'],
        ];

        foreach ($assets as $asset) {
            Asset::updateOrCreate(['internal_code' => $asset['internal_code']], $asset);
        }
    }
}
