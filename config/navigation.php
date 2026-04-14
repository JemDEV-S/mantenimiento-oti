<?php

return [
    'sidebar_modules' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'squares'],
        ['key' => 'assets', 'label' => 'Activos', 'route' => null, 'icon' => 'cube'],
        ['key' => 'maintenance', 'label' => 'Mantenimiento', 'route' => null, 'icon' => 'wrench'],
        ['key' => 'organization', 'label' => 'Organizacion', 'route' => null, 'icon' => 'building'],
        ['key' => 'documents', 'label' => 'Documentos', 'route' => null, 'icon' => 'document'],
        ['key' => 'users', 'label' => 'Usuarios', 'route' => null, 'icon' => 'users'],
    ],
    'sidebar_submenus' => [
        'assets' => [
            ['label' => 'Inventario', 'route' => 'assets.index', 'description' => 'Gestionar activos tecnologicos'],
            ['label' => 'Movimientos', 'route' => 'asset-movements.index', 'description' => 'Asignaciones y transferencias'],
        ],
        'maintenance' => [
            ['label' => 'Casos', 'route' => 'maintenance-cases.index', 'description' => 'Casos de mantenimiento'],
            ['label' => 'Campanas', 'route' => 'campaigns.index', 'description' => 'Campanas preventivas'],
        ],
        'organization' => [
            ['label' => 'Unidades', 'route' => 'organizational-units.index', 'description' => 'Estructura organizacional'],
            ['label' => 'Empleados', 'route' => 'employees.index', 'description' => 'Personal registrado'],
        ],
        'documents' => [
            ['label' => 'Documentos', 'route' => 'documents.index', 'description' => 'Fichas y actas generadas'],
        ],
        'users' => [
            ['label' => 'Usuarios', 'route' => 'users.index', 'description' => 'Gestionar cuentas del sistema'],
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'description' => 'Roles y permisos'],
            ['label' => 'Permisos', 'route' => 'admin.permissions.index', 'description' => 'Permisos granulares'],
        ],
    ],
    'command_palette_actions' => [
        ['label' => 'Ir al Dashboard', 'desc' => 'Vista general del sistema', 'icon' => 'squares', 'route' => 'dashboard'],
        ['label' => 'Nuevo activo', 'desc' => 'Registrar un activo tecnologico', 'icon' => 'cube', 'route' => 'assets.create'],
        ['label' => 'Nuevo caso de mantenimiento', 'desc' => 'Crear caso correctivo o preventivo', 'icon' => 'wrench', 'route' => 'maintenance-cases.create'],
        ['label' => 'Buscar empleado', 'desc' => 'Directorio de empleados', 'icon' => 'users', 'route' => 'employees.index'],
        ['label' => 'Ver movimientos', 'desc' => 'Historial de movimientos de activos', 'icon' => 'document', 'route' => 'asset-movements.index'],
    ],
];
