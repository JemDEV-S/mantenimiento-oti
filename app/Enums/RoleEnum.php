<?php
namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN           = 'admin';
    case TECNICO         = 'tecnico';
    case EMPLEADO        = 'empleado';
    case RESPONSABLE_OFICINA = 'responsable_oficina';

    public function label(): string
    {
        return match($this) {
            self::ADMIN         => 'Administrador OTI',
            self::TECNICO       => 'Técnico',
            self::EMPLEADO      => 'Empleado',
            self::RESPONSABLE_OFICINA => 'Responsable de Oficina',
        };
    }
}