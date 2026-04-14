<?php

namespace App\Enums;

enum DocumentType: string
{
    case ACTA_ENTREGA         = 'acta_entrega';
    case ORDEN_MANTENIMIENTO  = 'orden_mantenimiento';
    case INFORME_TECNICO      = 'informe_tecnico';
    case INVENTARIO           = 'inventario';
    case OTRO                 = 'otro';

    public function label(): string
    {
        return match($this) {
            self::ACTA_ENTREGA        => 'Acta de entrega',
            self::ORDEN_MANTENIMIENTO => 'Orden de mantenimiento',
            self::INFORME_TECNICO     => 'Informe técnico',
            self::INVENTARIO          => 'Inventario',
            self::OTRO                => 'Otro',
        };
    }
}
