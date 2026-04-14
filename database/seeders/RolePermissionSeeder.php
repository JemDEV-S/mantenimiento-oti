<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(PermissionEnum::cases())
            ->map(fn (PermissionEnum $permission) => Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]));

        $admin = Role::firstOrCreate([
            'name' => RoleEnum::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $tecnico = Role::firstOrCreate([
            'name' => RoleEnum::TECNICO->value,
            'guard_name' => 'web',
        ]);

        $empleado = Role::firstOrCreate([
            'name' => RoleEnum::EMPLEADO->value,
            'guard_name' => 'web',
        ]);

        $responsable = Role::firstOrCreate([
            'name' => RoleEnum::RESPONSABLE_OFICINA->value,
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions($permissions);

        $tecnico->syncPermissions([
            PermissionEnum::ASSET_VIEW->value,
            PermissionEnum::EMPLOYEE_VIEW->value,
            PermissionEnum::MAINTENANCE_CASE_VIEW->value,
            PermissionEnum::MAINTENANCE_CASE_CREATE->value,
            PermissionEnum::MAINTENANCE_CASE_EDIT->value,
            PermissionEnum::MAINTENANCE_CASE_CLOSE->value,
            PermissionEnum::CAMPAIGN_VIEW->value,
            PermissionEnum::DOCUMENT_VIEW->value,
            PermissionEnum::AGENT_VIEW->value,
        ]);

        $empleado->syncPermissions([
            PermissionEnum::ASSET_VIEW->value,
            PermissionEnum::EMPLOYEE_VIEW->value,
            PermissionEnum::MAINTENANCE_CASE_VIEW->value,
            PermissionEnum::DOCUMENT_VIEW->value,
        ]);

        $responsable->syncPermissions([
            PermissionEnum::ASSET_VIEW->value,
            PermissionEnum::ASSET_CREATE->value,
            PermissionEnum::ASSET_EDIT->value,
            PermissionEnum::ASSET_MOVEMENT_VIEW->value,
            PermissionEnum::ASSET_MOVEMENT_CREATE->value,
            PermissionEnum::EMPLOYEE_VIEW->value,
            PermissionEnum::ORG_UNIT_VIEW->value,
            PermissionEnum::MAINTENANCE_CASE_VIEW->value,
            PermissionEnum::CAMPAIGN_VIEW->value,
            PermissionEnum::DOCUMENT_VIEW->value,
        ]);
    }
}
