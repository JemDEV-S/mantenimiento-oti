<?php

namespace Database\Seeders;

use App\Enums\OrgUnitType;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class OrganizationalUnitSeeder extends Seeder
{
    public function run(): void
    {
        $gerencia = OrganizationalUnit::updateOrCreate(
            ['code' => 'GM'],
            [
                'name' => 'Gerencia Municipal',
                'type' => OrgUnitType::GERENCIA,
                'parent_id' => null,
                'is_active' => true,
                'sort_order' => 1,
                'meta_json' => ['scope' => 'institucional'],
            ]
        );

        $units = [
            ['code' => 'OTI', 'name' => 'Oficina de Tecnologias de Informacion', 'type' => OrgUnitType::OFICINA_GENERAL, 'sort_order' => 1],
            ['code' => 'ADM', 'name' => 'Oficina de Administracion', 'type' => OrgUnitType::OFICINA_GENERAL, 'sort_order' => 2],
            ['code' => 'TES', 'name' => 'Tesoreria', 'type' => OrgUnitType::OFICINA, 'sort_order' => 3],
            ['code' => 'OBR', 'name' => 'Infraestructura y Obras', 'type' => OrgUnitType::SUBGERENCIA, 'sort_order' => 4],
            ['code' => 'ALM', 'name' => 'Almacen Central', 'type' => OrgUnitType::SEDE, 'sort_order' => 5],
        ];

        foreach ($units as $unit) {
            OrganizationalUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit + [
                    'parent_id' => $gerencia->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
