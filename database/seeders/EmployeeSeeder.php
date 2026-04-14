<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $units = OrganizationalUnit::query()->get()->keyBy('code');

        $employees = [
            ['dni' => '70123456', 'name' => 'Carlos', 'last_name' => 'Ramirez Soto', 'email' => 'carlos.ramirez@mdsj.local', 'phone' => '987654321', 'position' => 'Jefe OTI', 'organizational_unit_id' => $units['OTI']->id, 'is_technician' => false, 'specialty' => 'Gestion TI', 'level' => 'senior', 'is_active' => true],
            ['dni' => '70234567', 'name' => 'Lucia', 'last_name' => 'Quispe Torres', 'email' => 'lucia.quispe@mdsj.local', 'phone' => '986123456', 'position' => 'Tecnico de Soporte', 'organizational_unit_id' => $units['OTI']->id, 'is_technician' => true, 'specialty' => 'Hardware y redes', 'level' => 'senior', 'is_active' => true],
            ['dni' => '70345678', 'name' => 'Diego', 'last_name' => 'Mendoza Paredes', 'email' => 'diego.mendoza@mdsj.local', 'phone' => '985456123', 'position' => 'Tecnico de Campo', 'organizational_unit_id' => $units['OTI']->id, 'is_technician' => true, 'specialty' => 'Mantenimiento preventivo', 'level' => 'mid', 'is_active' => true],
            ['dni' => '70456789', 'name' => 'Rosa', 'last_name' => 'Salazar Huaman', 'email' => 'rosa.salazar@mdsj.local', 'phone' => '984789123', 'position' => 'Responsable de Tesoreria', 'organizational_unit_id' => $units['TES']->id, 'is_technician' => false, 'specialty' => null, 'level' => 'senior', 'is_active' => true],
            ['dni' => '70567890', 'name' => 'Miguel', 'last_name' => 'Vargas Flores', 'email' => 'miguel.vargas@mdsj.local', 'phone' => '983222111', 'position' => 'Asistente Administrativo', 'organizational_unit_id' => $units['ADM']->id, 'is_technician' => false, 'specialty' => null, 'level' => 'mid', 'is_active' => true],
            ['dni' => '70678901', 'name' => 'Patricia', 'last_name' => 'Lopez Ccama', 'email' => 'patricia.lopez@mdsj.local', 'phone' => '982111333', 'position' => 'Coordinadora de Obras', 'organizational_unit_id' => $units['OBR']->id, 'is_technician' => false, 'specialty' => null, 'level' => 'senior', 'is_active' => true],
            ['dni' => '70789012', 'name' => 'Jorge', 'last_name' => 'Nina Apaza', 'email' => 'jorge.nina@mdsj.local', 'phone' => '981555777', 'position' => 'Encargado de Almacen', 'organizational_unit_id' => $units['ALM']->id, 'is_technician' => false, 'specialty' => 'Control patrimonial', 'level' => 'mid', 'is_active' => true],
        ];

        foreach ($employees as $employee) {
            $employee['full_name'] = "{$employee['name']} {$employee['last_name']}";
            Employee::updateOrCreate(['dni' => $employee['dni']], $employee);
        }

        $unitResponsibles = ['OTI' => '70123456', 'TES' => '70456789', 'ADM' => '70567890', 'OBR' => '70678901', 'ALM' => '70789012'];

        foreach ($unitResponsibles as $unitCode => $dni) {
            $unit = $units[$unitCode] ?? null;
            $employee = Employee::where('dni', $dni)->first();

            if ($unit && $employee) {
                $unit->update(['responsible_employee_id' => $employee->id]);
            }
        }
    }
}
