<?php
namespace App\Enums;

enum OrgUnitType: string
{
    case GERENCIA = 'gerencia';
    case OFICINA_GENERAL = 'oficina_general';
    case SUBGERENCIA = 'subgerencia';
    case OFICINA = 'oficina';
    case SEDE = 'sede';

    public function label(): string
    {
        return match ($this) {
            self::GERENCIA => 'Gerencia',
            self::OFICINA_GENERAL => 'Oficina General',
            self::SUBGERENCIA => 'Subgerencia',
            self::OFICINA => 'Oficina',
            self::SEDE => 'Sede',
        };
    }
}