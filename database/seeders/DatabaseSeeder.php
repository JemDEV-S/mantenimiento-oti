<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            OrganizationalUnitSeeder::class,
            EmployeeSeeder::class,
            SuperAdminSeeder::class,
            UserSeeder::class,
            AssetSeeder::class,
            AssetMovementSeeder::class,
            MaintenanceSeeder::class,
            DocumentSeeder::class,
            SettingSeeder::class,
            AgentSeeder::class,
        ]);
    }
}
