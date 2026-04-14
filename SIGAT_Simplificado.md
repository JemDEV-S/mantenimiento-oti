# Guía de Desarrollo — SIGAT MVP
## Sistema Integral de Gestión de Activos Tecnológicos

---

## Índice

1. Visión general y decisiones técnicas
2. Stack tecnológico
3. Estructura del proyecto
4. Modelo de datos (migraciones)
5. Mejoras aplicadas al diseño original
6. Orden de implementación paso a paso
7. Módulo 1 — Seguridad
8. Módulo 2 — Organización
9. Módulo 3 — Activos
10. Módulo 4 — Mantenimiento
11. Módulo 5 — Soporte
12. Seeders y datos iniciales
13. API endpoints
14. Validaciones clave
15. Políticas de autorización
16. Testing
17. Despliegue
18. Roadmap post-MVP

---

## 1. Visión general y decisiones técnicas

### ¿Qué es el MVP?

El MVP del SIGAT cubre las **Fases 1 y 2** del documento original:

- Seguridad (usuarios, roles, permisos).
- Organización (unidades, empleados, proveedores).
- Activos (inventario, adjuntos, movimientos).
- Mantenimiento (casos individuales + campañas).
- Soporte (documentos, auditoría, configuración, notificaciones).

Las integraciones con agente local e IA se dejan para Fase 3, pero el modelo de datos ya las contempla.

### Decisiones clave

| Decisión | Elección | Razón |
|----------|----------|-------|
| Framework | Laravel 11 | Estabilidad, ecosistema, velocidad de desarrollo |
| Frontend | Blade + Livewire 3 | Sin SPA, menos complejidad, interactividad donde se necesita |
| CSS | Tailwind CSS 3 | Consistencia visual, rápido de construir |
| Componentes UI | Livewire Volt + Alpine.js | Componentes reactivos sin JavaScript pesado |
| Base de datos | PostgreSQL 16 | Soporte nativo JSON, mejor rendimiento en consultas complejas |
| Autenticación | Laravel Breeze (customizado) | Simple, sin overhead de Jetstream |
| Autorización | Policies + Gates nativos | Sin paquetes extra; escalar a Spatie si se necesita |
| PDF | DomPDF vía laravel-dompdf | Suficiente para documentos operativos |
| Excel | Maatwebsite/Excel 3 | Exportaciones estándar |
| Auditoría | spatie/laravel-activitylog | Madura, probada, menos código manual |
| Notificaciones | Laravel Notifications (database + mail) | Nativo, extensible |
| Almacenamiento | Laravel Storage (local, migrable a S3) | Flexible |
| Cache | Redis | Sessions, cache, queues |

---

## 2. Stack tecnológico — Requisitos

```
PHP >= 8.2
Composer >= 2.6
Node.js >= 20 LTS
PostgreSQL >= 16
Redis >= 7
```

### Paquetes principales (composer.json)

```json
{
    "require": {
        "laravel/framework": "^11.0",
        "livewire/livewire": "^3.0",
        "livewire/volt": "^1.0",
        "barryvdh/laravel-dompdf": "^2.0",
        "maatwebsite/excel": "^3.1",
        "spatie/laravel-activitylog": "^4.0",
        "spatie/laravel-medialibrary": "^11.0"
    },
    "require-dev": {
        "pestphp/pest": "^2.0",
        "pestphp/pest-plugin-laravel": "^2.0",
        "laravel/pint": "^1.0"
    }
}
```

> **Mejora aplicada:** Se reemplaza la tabla `attachments` manual por `spatie/laravel-medialibrary`. Es más robusta, maneja conversiones de imagen, asocia archivos a cualquier modelo vía polimorfismo y es estándar en el ecosistema Laravel.

---

## 3. Estructura del proyecto

```
sigat/
├── app/
│   ├── Enums/                    # Enums PHP 8.1+
│   │   ├── AssetStatus.php
│   │   ├── AssetCondition.php
│   │   ├── AssetType.php
│   │   ├── MaintenanceType.php
│   │   ├── MaintenanceStatus.php
│   │   ├── MovementType.php
│   │   ├── CampaignStatus.php
│   │   ├── OrgUnitType.php
│   │   └── Priority.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── AssetController.php
│   │   │   ├── AssetMovementController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DocumentController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── MaintenanceCaseController.php
│   │   │   ├── OrgUnitController.php
│   │   │   ├── ReportController.php
│   │   │   ├── RoleController.php
│   │   │   ├── SettingController.php
│   │   │   ├── SupplierController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/
│   │   │   └── EnsurePermission.php
│   │   └── Requests/              # Form Requests por entidad
│   ├── Livewire/                  # Componentes Livewire
│   │   ├── Assets/
│   │   │   ├── AssetTable.php
│   │   │   ├── AssetForm.php
│   │   │   └── AssetDetail.php
│   │   ├── Maintenance/
│   │   └── Campaigns/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php         # MEJORA: tabla de permisos
│   │   ├── OrgUnit.php
│   │   ├── Employee.php
│   │   ├── Supplier.php
│   │   ├── Asset.php
│   │   ├── AssetMovement.php
│   │   ├── MaintenanceCase.php
│   │   ├── MaintenanceItem.php
│   │   ├── MaintenanceCampaign.php
│   │   ├── CampaignAsset.php
│   │   ├── Document.php
│   │   ├── Setting.php
│   │   ├── Notification.php
│   │   ├── AgentDevice.php
│   │   └── AgentSync.php
│   ├── Policies/                  # Autorización por modelo
│   ├── Services/                  # Lógica de negocio
│   │   ├── AssetService.php
│   │   ├── MaintenanceService.php
│   │   ├── CampaignService.php
│   │   ├── DocumentService.php
│   │   ├── ReportService.php
│   │   └── AuditService.php
│   ├── Observers/                 # Observadores para auditoría automática
│   └── Traits/
│       ├── HasAuditLog.php
│       └── GeneratesCode.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── guest.blade.php
│   │   │   └── partials/
│   │   ├── components/            # Componentes Blade reutilizables
│   │   │   ├── table.blade.php
│   │   │   ├── modal.blade.php
│   │   │   ├── badge.blade.php
│   │   │   ├── stat-card.blade.php
│   │   │   └── form/
│   │   ├── assets/
│   │   ├── maintenance/
│   │   ├── campaigns/
│   │   ├── organization/
│   │   ├── documents/
│   │   │   └── templates/         # Plantillas PDF
│   │   ├── reports/
│   │   └── settings/
│   └── css/
├── routes/
│   ├── web.php
│   └── api.php                    # Para agente local
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 4. Modelo de datos — Migraciones

A continuación se presentan **todas las migraciones** del MVP en orden de ejecución. Se incluyen las mejoras aplicadas.

### 4.1. Seguridad

```php
// database/migrations/0001_create_roles_table.php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->text('description')->nullable();
    $table->boolean('is_system')->default(false); // No editable/eliminable
    $table->timestamps();
});

// MEJORA: Tabla de permisos granulares
// database/migrations/0002_create_permissions_table.php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);           // "Crear activos"
    $table->string('slug', 100)->unique();  // "assets.create"
    $table->string('module', 50);           // "assets"
    $table->timestamps();
});

Schema::create('role_permission', function (Blueprint $table) {
    $table->id();
    $table->foreignId('role_id')->constrained()->cascadeOnDelete();
    $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
    $table->unique(['role_id', 'permission_id']);
});
```

> **Mejora:** El diseño original solo tenía `roles`. Se agrega `permissions` y `role_permission` porque "restricción de acceso según permisos del rol" sin una tabla de permisos obliga a hardcodear todo. Con esta tabla se puede controlar acceso granularmente desde la UI.

```php
// database/migrations/0003_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->foreignId('role_id')->constrained();
    $table->unsignedBigInteger('employee_id')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    // FK de employee_id se agrega después de crear employees
});
```

### 4.2. Organización

```php
// database/migrations/0004_create_organizational_units_table.php
Schema::create('organizational_units', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->string('type', 30);            // Enum: sede, gerencia, subgerencia, etc.
    $table->string('code', 30)->unique();
    $table->string('name');
    $table->unsignedBigInteger('responsible_employee_id')->nullable();
    $table->jsonb('meta_json')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedSmallInteger('sort_order')->default(0); // MEJORA
    $table->timestamps();

    $table->foreign('parent_id')
          ->references('id')->on('organizational_units')
          ->nullOnDelete();

    $table->index('parent_id');
    $table->index('type');
});
```

> **Mejora:** Se agrega `sort_order` para controlar el orden de visualización dentro de un mismo nivel jerárquico. También se usa `jsonb` en vez de `json` para PostgreSQL (indexable, más rápido).

```php
// database/migrations/0005_create_employees_table.php
Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('dni', 20)->unique();
    $table->string('full_name');
    $table->string('email')->nullable();
    $table->string('phone', 30)->nullable();
    $table->string('position', 100)->nullable();
    $table->foreignId('organizational_unit_id')
          ->nullable()->constrained()->nullOnDelete();
    $table->boolean('is_technician')->default(false);
    $table->string('specialty', 100)->nullable();
    $table->string('level', 30)->nullable();  // junior, mid, senior
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('organizational_unit_id');
    $table->index('is_technician');
});

// Agregar FK pendientes
Schema::table('users', function (Blueprint $table) {
    $table->foreign('employee_id')
          ->references('id')->on('employees')
          ->nullOnDelete();
});

Schema::table('organizational_units', function (Blueprint $table) {
    $table->foreign('responsible_employee_id')
          ->references('id')->on('employees')
          ->nullOnDelete();
});
```

```php
// database/migrations/0006_create_suppliers_table.php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('ruc', 20)->unique();
    $table->string('business_name');
    $table->string('contact_name')->nullable();
    $table->string('phone', 30)->nullable();
    $table->string('email')->nullable();
    $table->string('address')->nullable();
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true); // MEJORA
    $table->timestamps();
});
```

> **Mejora:** Se agrega `is_active` a proveedores, que el original omitió. Es necesario para desactivar proveedores sin borrarlos.

### 4.3. Activos

```php
// database/migrations/0007_create_assets_table.php
Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('internal_code', 50)->unique();
    $table->string('patrimonial_code', 50)->nullable()->unique();
    $table->string('name');
    $table->string('asset_type', 50);           // Enum AssetType
    $table->string('brand', 100)->nullable();
    $table->string('model', 100)->nullable();
    $table->string('serial_number', 100)->nullable();
    $table->string('status', 30)->default('disponible');      // Enum AssetStatus
    $table->string('condition', 30)->default('bueno');        // Enum AssetCondition
    $table->foreignId('organizational_unit_id')
          ->nullable()->constrained()->nullOnDelete();
    $table->foreignId('responsible_employee_id')
          ->nullable()->constrained('employees')->nullOnDelete();
    $table->foreignId('supplier_id')
          ->nullable()->constrained()->nullOnDelete();
    $table->date('purchase_date')->nullable();
    $table->decimal('reference_value', 12, 2)->nullable();
    $table->jsonb('specs_json')->nullable();     // CPU, RAM, disco, etc.
    $table->jsonb('extra_json')->nullable();     // Garantía, licencias, etc.
    $table->text('notes')->nullable();
    $table->softDeletes();                       // MEJORA
    $table->timestamps();

    $table->index('asset_type');
    $table->index('status');
    $table->index('organizational_unit_id');
    $table->index('responsible_employee_id');
});
```

> **Mejora:** Se agrega `softDeletes()`. Eliminar un activo del inventario sin poder recuperarlo es un riesgo operativo grave en una municipalidad. Con soft deletes se mantiene la integridad referencial y se puede restaurar.

```php
// database/migrations/0008_create_asset_movements_table.php
Schema::create('asset_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
    $table->string('movement_type', 30);     // asignacion, devolucion, transferencia, baja
    $table->unsignedBigInteger('origin_unit_id')->nullable();
    $table->unsignedBigInteger('destination_unit_id')->nullable();
    $table->unsignedBigInteger('from_employee_id')->nullable();
    $table->unsignedBigInteger('to_employee_id')->nullable();
    $table->date('movement_date');
    $table->string('reason')->nullable();
    $table->string('document_number', 50)->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('created_by');
    $table->timestamps();

    $table->foreign('origin_unit_id')->references('id')->on('organizational_units')->nullOnDelete();
    $table->foreign('destination_unit_id')->references('id')->on('organizational_units')->nullOnDelete();
    $table->foreign('from_employee_id')->references('id')->on('employees')->nullOnDelete();
    $table->foreign('to_employee_id')->references('id')->on('employees')->nullOnDelete();
    $table->foreign('created_by')->references('id')->on('users');

    $table->index('asset_id');
    $table->index('movement_date');
});
```

### 4.4. Mantenimiento

```php
// database/migrations/0009_create_maintenance_campaigns_table.php
// Se crea ANTES que maintenance_cases porque cases referencia campaigns
Schema::create('maintenance_campaigns', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->string('name');
    $table->text('objective')->nullable();
    $table->jsonb('scope_json')->nullable();
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->string('status', 30)->default('planificada');
    $table->foreignId('coordinator_employee_id')
          ->nullable()->constrained('employees')->nullOnDelete();
    $table->text('summary')->nullable();
    $table->jsonb('metrics_json')->nullable();
    $table->unsignedBigInteger('created_by');
    $table->timestamps();

    $table->foreign('created_by')->references('id')->on('users');
    $table->index('status');
});
```

```php
// database/migrations/0010_create_maintenance_cases_table.php
Schema::create('maintenance_cases', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
    $table->foreignId('campaign_id')
          ->nullable()->constrained('maintenance_campaigns')->nullOnDelete();
    $table->unsignedBigInteger('reported_by_employee_id')->nullable();
    $table->unsignedBigInteger('assigned_technician_id')->nullable();
    $table->string('maintenance_type', 30);  // preventivo, correctivo, diagnostico, emergencia
    $table->string('priority', 20)->default('media');
    $table->string('status', 30)->default('registrado');
    $table->text('problem_description')->nullable();
    $table->text('diagnosis')->nullable();
    $table->text('actions_taken')->nullable();
    $table->timestamp('started_at')->nullable();
    $table->timestamp('finished_at')->nullable();
    $table->date('next_maintenance_date')->nullable();
    $table->string('conformity_name')->nullable();
    $table->timestamp('conformity_date')->nullable();
    $table->decimal('total_cost', 12, 2)->default(0);
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('created_by');
    $table->timestamps();

    $table->foreign('reported_by_employee_id')->references('id')->on('employees')->nullOnDelete();
    $table->foreign('assigned_technician_id')->references('id')->on('employees')->nullOnDelete();
    $table->foreign('created_by')->references('id')->on('users');

    $table->index(['asset_id', 'status']);
    $table->index('campaign_id');
    $table->index('assigned_technician_id');
});
```

```php
// database/migrations/0011_create_maintenance_items_table.php
Schema::create('maintenance_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('maintenance_case_id')->constrained()->cascadeOnDelete();
    $table->string('item_type', 30);   // tarea, repuesto, servicio, costo
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('quantity', 10, 2)->default(1);
    $table->decimal('unit_cost', 12, 2)->default(0);
    $table->decimal('total_cost', 12, 2)->default(0);
    $table->jsonb('data_json')->nullable();
    $table->timestamps();
});
```

```php
// database/migrations/0012_create_campaign_assets_table.php
Schema::create('campaign_assets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('campaign_id')->constrained('maintenance_campaigns')->cascadeOnDelete();
    $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
    $table->unsignedBigInteger('assigned_technician_id')->nullable();
    $table->date('scheduled_date')->nullable();
    $table->date('attended_date')->nullable();
    $table->string('status', 30)->default('pendiente');
    $table->unsignedBigInteger('maintenance_case_id')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('assigned_technician_id')->references('id')->on('employees')->nullOnDelete();
    $table->foreign('maintenance_case_id')->references('id')->on('maintenance_cases')->nullOnDelete();

    $table->unique(['campaign_id', 'asset_id']);
});
```

### 4.5. Soporte

```php
// database/migrations/0013_create_documents_table.php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->string('document_type', 50);     // ficha_tecnica, acta_mantenimiento, etc.
    $table->string('reference_type', 50);     // asset, maintenance_case, campaign, movement
    $table->unsignedBigInteger('reference_id');
    $table->string('code', 50)->nullable();
    $table->string('title');
    $table->string('file_path');
    $table->unsignedBigInteger('generated_by');
    $table->timestamp('generated_at');
    $table->jsonb('meta_json')->nullable();
    $table->timestamps();

    $table->foreign('generated_by')->references('id')->on('users');
    $table->index(['reference_type', 'reference_id']);
});
```

```php
// database/migrations/0014_create_settings_table.php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique();
    $table->text('value')->nullable();
    $table->string('type', 20)->default('string'); // string, integer, boolean, json
    $table->string('group_name', 50)->default('general');
    $table->string('description')->nullable();
    $table->boolean('is_sensitive')->default(false);
    $table->timestamps();
});
```

```php
// database/migrations/0015_create_notifications_table.php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
// Se usa la tabla nativa de Laravel Notifications
```

> **Mejora:** Se reemplaza la tabla custom de notificaciones por la nativa de Laravel (`php artisan notifications:table`). Es compatible con el sistema de Notifications de Laravel, soporta múltiples canales (database, mail, SMS) y evita reinventar la rueda.

```php
// database/migrations/0016_create_agent_devices_table.php
Schema::create('agent_devices', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
    $table->string('hostname')->nullable();
    $table->string('serial_number', 100)->nullable();
    $table->string('device_model')->nullable();
    $table->string('operating_system')->nullable();
    $table->string('agent_version', 30)->nullable();
    $table->string('last_ip', 45)->nullable();
    $table->timestamp('last_heartbeat_at')->nullable();
    $table->string('status', 20)->default('activo');
    $table->jsonb('last_snapshot_json')->nullable();
    $table->string('api_token', 80)->unique();  // MEJORA: token de autenticación
    $table->timestamps();
});
```

> **Mejora:** Se agrega `api_token` directamente en la tabla. El documento original menciona "autenticación por token de dispositivo" pero no incluía el campo.

```php
// database/migrations/0017_create_agent_syncs_table.php
Schema::create('agent_syncs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_device_id')->constrained()->cascadeOnDelete();
    $table->string('sync_type', 30);      // heartbeat, snapshot, change
    $table->jsonb('payload_json')->nullable();
    $table->jsonb('detected_changes_json')->nullable();
    $table->string('status', 20)->default('recibido');
    $table->timestamp('synced_at');
    $table->timestamps();

    $table->index(['agent_device_id', 'synced_at']);
});
```

```php
// database/migrations/0018_create_ai_logs_table.php
Schema::create('ai_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('context_type', 50)->nullable();
    $table->unsignedBigInteger('context_id')->nullable();
    $table->text('prompt');
    $table->text('response')->nullable();
    $table->string('status', 20)->default('pendiente');
    $table->string('applied_action')->nullable();
    $table->jsonb('meta_json')->nullable();
    $table->timestamps();

    $table->index(['context_type', 'context_id']);
});
```

---

## 5. Mejoras aplicadas al diseño original

| # | Área | Mejora | Justificación |
|---|------|--------|---------------|
| 1 | Seguridad | Tabla `permissions` + `role_permission` | Sin ella no hay control granular real |
| 2 | Roles | Campo `is_system` | Evita que eliminen roles críticos (admin) |
| 3 | Org. Units | Campo `sort_order` | Control de orden visual |
| 4 | JSON | Usar `jsonb` en vez de `json` | PostgreSQL: indexable, validable, más rápido |
| 5 | Proveedores | Campo `is_active` | Desactivar sin borrar |
| 6 | Activos | `softDeletes()` | Protección contra eliminaciones accidentales |
| 7 | Adjuntos | `spatie/medialibrary` en vez de tabla manual | Más robusto, thumbnails, conversiones |
| 8 | Notificaciones | Tabla nativa Laravel Notifications | Compatible con canales múltiples |
| 9 | Agente | Campo `api_token` en `agent_devices` | Token de autenticación que faltaba |
| 10 | Auditoría | `spatie/laravel-activitylog` en vez de tabla manual | Menos código, más confiable |
| 11 | Códigos | Trait `GeneratesCode` | Generación automática de códigos secuenciales |

---

## 6. Orden de implementación paso a paso

### Sprint 1 (Semana 1-2): Fundación

```
1. Crear proyecto Laravel
2. Configurar .env, base de datos, Redis
3. Ejecutar migraciones 0001–0006 (seguridad + organización)
4. Implementar autenticación (Breeze customizado)
5. Crear layout principal (sidebar, header, breadcrumbs)
6. CRUD de Roles + Permisos
7. CRUD de Usuarios
8. CRUD de Unidades Organizacionales (con árbol jerárquico)
9. CRUD de Empleados
10. CRUD de Proveedores
11. Seeders iniciales
```

### Sprint 2 (Semana 3-4): Activos

```
1. Ejecutar migraciones 0007–0008
2. Configurar spatie/medialibrary
3. CRUD completo de Activos
4. Filtros avanzados (tipo, estado, unidad, responsable)
5. Detalle del activo con pestañas
6. Carga de adjuntos (fotos, facturas, garantías)
7. Registro de movimientos / asignaciones
8. Historial del activo (movimientos + mantenimientos)
9. Auditoría automática con activity-log
```

### Sprint 3 (Semana 5-6): Mantenimiento

```
1. Ejecutar migraciones 0009–0012
2. CRUD de Casos de Mantenimiento
3. Flujo completo: registrar → asignar → diagnosticar → resolver → cerrar
4. Ítems de mantenimiento (tareas, repuestos, costos)
5. Cálculo automático de total_cost
6. Programación de siguiente mantenimiento
7. CRUD de Campañas
8. Carga masiva de activos a campaña
9. Control de avance por activo
10. Cierre de campaña con resumen
```

### Sprint 4 (Semana 7-8): Soporte + Pulido

```
1. Ejecutar migraciones 0013–0018
2. Generación de documentos PDF (5 tipos)
3. Configuración del sistema
4. Notificaciones (asignación técnico, cierre mantenimiento, campaña)
5. Reportes (pantalla + exportación Excel)
6. Dashboard con indicadores
7. Revisión de permisos en todas las vistas
8. Testing funcional
9. Deploy
```

---

## 7. Módulo 1 — Seguridad (Detalle de implementación)

### 7.1. Enums

```php
// app/Enums no aplica directamente aquí, pero sí:
// Los permisos se definen por módulo + acción

// Ejemplo de slugs de permisos:
// assets.view, assets.create, assets.edit, assets.delete
// assets.movements, assets.attachments
// maintenance.view, maintenance.create, maintenance.assign
// maintenance.close, maintenance.items
// campaigns.view, campaigns.create, campaigns.manage
// documents.generate, documents.view
// reports.view, reports.export
// organization.view, organization.manage
// users.view, users.manage
// settings.manage
// audit.view
```

### 7.2. Modelo Role

```php
// app/Models/Role.php
class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system'];

    protected $casts = ['is_system' => 'boolean'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions()->where('slug', $slug)->exists();
    }
}
```

### 7.3. Modelo User

```php
// app/Models/User.php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'employee_id', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function can($ability, $arguments = []): bool
    {
        // Admins can do everything
        if ($this->role->slug === 'admin') {
            return true;
        }

        // Check permission slug
        if (is_string($ability) && str_contains($ability, '.')) {
            return $this->role->hasPermission($ability);
        }

        return parent::can($ability, $arguments);
    }
}
```

### 7.4. Middleware de permisos

```php
// app/Http/Middleware/EnsurePermission.php
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->can($permission)) {
            abort(403, 'No tienes permiso para esta acción.');
        }
        return $next($request);
    }
}

// Registro en bootstrap/app.php:
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'perm' => EnsurePermission::class,
    ]);
})
```

### 7.5. Rutas de seguridad

```php
// routes/web.php (extracto)
Route::middleware(['auth'])->group(function () {

    Route::middleware('perm:users.manage')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('perm:users.manage')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])
             ->name('roles.permissions');
    });
});
```

---

## 8. Módulo 2 — Organización (Detalle)

### 8.1. Enum OrgUnitType

```php
// app/Enums/OrgUnitType.php
enum OrgUnitType: string
{
    case SEDE = 'sede';
    case GERENCIA = 'gerencia';
    case SUBGERENCIA = 'subgerencia';
    case OFICINA = 'oficina';
    case AREA = 'area';
    case UBICACION = 'ubicacion';

    public function label(): string
    {
        return match ($this) {
            self::SEDE => 'Sede',
            self::GERENCIA => 'Gerencia',
            self::SUBGERENCIA => 'Subgerencia',
            self::OFICINA => 'Oficina',
            self::AREA => 'Área',
            self::UBICACION => 'Ubicación',
        };
    }
}
```

### 8.2. Modelo OrgUnit

```php
// app/Models/OrgUnit.php
class OrgUnit extends Model
{
    protected $table = 'organizational_units';

    protected $fillable = [
        'parent_id', 'type', 'code', 'name',
        'responsible_employee_id', 'meta_json',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'type' => OrgUnitType::class,
        'meta_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'organizational_unit_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'organizational_unit_id');
    }

    // Obtener ruta completa: "Sede Central > Gerencia TI > Soporte"
    public function getFullPathAttribute(): string
    {
        $path = collect([$this->name]);
        $current = $this;
        while ($current->parent) {
            $current = $current->parent;
            $path->prepend($current->name);
        }
        return $path->implode(' > ');
    }
}
```

### 8.3. Modelo Employee

```php
// app/Models/Employee.php
class Employee extends Model
{
    protected $fillable = [
        'dni', 'full_name', 'email', 'phone', 'position',
        'organizational_unit_id', 'is_technician',
        'specialty', 'level', 'is_active',
    ];

    protected $casts = [
        'is_technician' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'organizational_unit_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function assignedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'responsible_employee_id');
    }

    public function maintenanceCases(): HasMany
    {
        return $this->hasMany(MaintenanceCase::class, 'assigned_technician_id');
    }

    // Scope: solo técnicos activos
    public function scopeTechnicians(Builder $query): Builder
    {
        return $query->where('is_technician', true)->where('is_active', true);
    }
}
```

---

## 9. Módulo 3 — Activos (Detalle)

### 9.1. Enums

```php
// app/Enums/AssetType.php
enum AssetType: string
{
    case PC_ESCRITORIO = 'pc_escritorio';
    case LAPTOP = 'laptop';
    case IMPRESORA = 'impresora';
    case ESCANER = 'escaner';
    case UPS = 'ups';
    case MONITOR = 'monitor';
    case SERVIDOR = 'servidor';
    case SWITCH = 'switch';
    case ROUTER = 'router';
    case ACCESS_POINT = 'access_point';
    case CAMARA = 'camara';
    case PROYECTOR = 'proyector';
    case TABLET = 'tablet';
    case BIOMETRICO = 'biometrico';
    case PERIFERICO = 'periferico';
    case COMPONENTE = 'componente';
    case OTRO = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::PC_ESCRITORIO => 'PC de Escritorio',
            self::LAPTOP => 'Laptop',
            self::IMPRESORA => 'Impresora',
            self::ESCANER => 'Escáner',
            self::UPS => 'UPS',
            self::MONITOR => 'Monitor',
            self::SERVIDOR => 'Servidor',
            self::SWITCH => 'Switch',
            self::ROUTER => 'Router',
            self::ACCESS_POINT => 'Punto de Acceso',
            self::CAMARA => 'Cámara',
            self::PROYECTOR => 'Proyector',
            self::TABLET => 'Tablet',
            self::BIOMETRICO => 'Equipo Biométrico',
            self::PERIFERICO => 'Periférico',
            self::COMPONENTE => 'Componente',
            self::OTRO => 'Otro',
        };
    }
}

// app/Enums/AssetStatus.php
enum AssetStatus: string
{
    case DISPONIBLE = 'disponible';
    case ASIGNADO = 'asignado';
    case EN_MANTENIMIENTO = 'en_mantenimiento';
    case EN_ALMACEN = 'en_almacen';
    case DE_BAJA = 'de_baja';
    case PRESTAMO = 'prestamo';

    public function label(): string { /* ... */ }
    public function color(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'green',
            self::ASIGNADO => 'blue',
            self::EN_MANTENIMIENTO => 'yellow',
            self::EN_ALMACEN => 'gray',
            self::DE_BAJA => 'red',
            self::PRESTAMO => 'purple',
        };
    }
}

// app/Enums/AssetCondition.php
enum AssetCondition: string
{
    case BUENO = 'bueno';
    case REGULAR = 'regular';
    case MALO = 'malo';
    case INOPERATIVO = 'inoperativo';
}

// app/Enums/MovementType.php
enum MovementType: string
{
    case ASIGNACION = 'asignacion';
    case DEVOLUCION = 'devolucion';
    case TRANSFERENCIA = 'transferencia';
    case PRESTAMO = 'prestamo';
    case BAJA = 'baja';
}
```

### 9.2. Modelo Asset

```php
// app/Models/Asset.php
class Asset extends Model
{
    use SoftDeletes, HasFactory;
    use InteractsWithMedia; // spatie/medialibrary

    protected $fillable = [
        'uuid', 'internal_code', 'patrimonial_code', 'name',
        'asset_type', 'brand', 'model', 'serial_number',
        'status', 'condition', 'organizational_unit_id',
        'responsible_employee_id', 'supplier_id',
        'purchase_date', 'reference_value',
        'specs_json', 'extra_json', 'notes',
    ];

    protected $casts = [
        'asset_type' => AssetType::class,
        'status' => AssetStatus::class,
        'condition' => AssetCondition::class,
        'specs_json' => 'array',
        'extra_json' => 'array',
        'purchase_date' => 'date',
        'reference_value' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            $asset->uuid ??= (string) Str::uuid();
        });
    }

    // --- Relaciones ---

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'organizational_unit_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class)->orderByDesc('movement_date');
    }

    public function maintenanceCases(): HasMany
    {
        return $this->hasMany(MaintenanceCase::class);
    }

    public function agentDevice(): HasOne
    {
        return $this->hasOne(AgentDevice::class);
    }

    // --- Media collections ---

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
        $this->addMediaCollection('documents');  // facturas, garantías
    }

    // --- Scopes ---

    public function scopeFilterBy(Builder $q, array $filters): Builder
    {
        return $q
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('asset_type', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['condition'] ?? null, fn ($q, $v) => $q->where('condition', $v))
            ->when($filters['unit_id'] ?? null, fn ($q, $v) => $q->where('organizational_unit_id', $v))
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('responsible_employee_id', $v))
            ->when($filters['search'] ?? null, fn ($q, $v) =>
                $q->where(fn ($q) =>
                    $q->where('name', 'ilike', "%{$v}%")
                      ->orWhere('internal_code', 'ilike', "%{$v}%")
                      ->orWhere('patrimonial_code', 'ilike', "%{$v}%")
                      ->orWhere('serial_number', 'ilike', "%{$v}%")
                )
            );
    }
}
```

### 9.3. Service: AssetService

```php
// app/Services/AssetService.php
class AssetService
{
    /**
     * Registra un movimiento y actualiza el activo.
     */
    public function registerMovement(
        Asset $asset,
        MovementType $type,
        array $data,
        User $user
    ): AssetMovement {
        return DB::transaction(function () use ($asset, $type, $data, $user) {

            $movement = $asset->movements()->create([
                'movement_type' => $type->value,
                'origin_unit_id' => $asset->organizational_unit_id,
                'destination_unit_id' => $data['destination_unit_id'] ?? null,
                'from_employee_id' => $asset->responsible_employee_id,
                'to_employee_id' => $data['to_employee_id'] ?? null,
                'movement_date' => $data['movement_date'] ?? now(),
                'reason' => $data['reason'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            // Actualizar activo
            $updates = [];
            if (isset($data['destination_unit_id'])) {
                $updates['organizational_unit_id'] = $data['destination_unit_id'];
            }
            if (array_key_exists('to_employee_id', $data)) {
                $updates['responsible_employee_id'] = $data['to_employee_id'];
            }
            if ($type === MovementType::BAJA) {
                $updates['status'] = AssetStatus::DE_BAJA->value;
            } elseif ($type === MovementType::ASIGNACION) {
                $updates['status'] = AssetStatus::ASIGNADO->value;
            } elseif ($type === MovementType::DEVOLUCION) {
                $updates['status'] = AssetStatus::DISPONIBLE->value;
                $updates['responsible_employee_id'] = null;
            }

            if ($updates) {
                $asset->update($updates);
            }

            return $movement;
        });
    }
}
```

### 9.4. Trait GeneratesCode

```php
// app/Traits/GeneratesCode.php
trait GeneratesCode
{
    public static function generateCode(string $prefix): string
    {
        $year = now()->format('Y');
        $last = static::where('code', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('code')
            ->value('code');

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $next = (int) end($parts) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $next);
    }
}
```

---

## 10. Módulo 4 — Mantenimiento (Detalle)

### 10.1. Enums

```php
// app/Enums/MaintenanceType.php
enum MaintenanceType: string
{
    case PREVENTIVO = 'preventivo';
    case CORRECTIVO = 'correctivo';
    case DIAGNOSTICO = 'diagnostico';
    case EMERGENCIA = 'emergencia';
}

// app/Enums/MaintenanceStatus.php
enum MaintenanceStatus: string
{
    case REGISTRADO = 'registrado';
    case ASIGNADO = 'asignado';
    case EN_PROCESO = 'en_proceso';
    case ATENDIDO = 'atendido';
    case OBSERVADO = 'observado';
    case CERRADO = 'cerrado';
    case CANCELADO = 'cancelado';

    public function canTransitionTo(self $target): bool
    {
        $allowed = match ($this) {
            self::REGISTRADO => [self::ASIGNADO, self::CANCELADO],
            self::ASIGNADO => [self::EN_PROCESO, self::CANCELADO],
            self::EN_PROCESO => [self::ATENDIDO, self::OBSERVADO],
            self::ATENDIDO => [self::CERRADO, self::OBSERVADO],
            self::OBSERVADO => [self::EN_PROCESO, self::CANCELADO],
            self::CERRADO, self::CANCELADO => [],
        };
        return in_array($target, $allowed);
    }
}

// app/Enums/CampaignStatus.php
enum CampaignStatus: string
{
    case PLANIFICADA = 'planificada';
    case EN_EJECUCION = 'en_ejecucion';
    case PAUSADA = 'pausada';
    case CERRADA = 'cerrada';
    case CANCELADA = 'cancelada';
}

// app/Enums/Priority.php
enum Priority: string
{
    case BAJA = 'baja';
    case MEDIA = 'media';
    case ALTA = 'alta';
    case CRITICA = 'critica';
}
```

### 10.2. Modelo MaintenanceCase

```php
// app/Models/MaintenanceCase.php
class MaintenanceCase extends Model
{
    use GeneratesCode;

    protected $fillable = [
        'code', 'asset_id', 'campaign_id',
        'reported_by_employee_id', 'assigned_technician_id',
        'maintenance_type', 'priority', 'status',
        'problem_description', 'diagnosis', 'actions_taken',
        'started_at', 'finished_at', 'next_maintenance_date',
        'conformity_name', 'conformity_date', 'total_cost',
        'notes', 'created_by',
    ];

    protected $casts = [
        'maintenance_type' => MaintenanceType::class,
        'priority' => Priority::class,
        'status' => MaintenanceStatus::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'conformity_date' => 'datetime',
        'next_maintenance_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $case) {
            $case->code ??= self::generateCode('MNT');
        });
    }

    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(MaintenanceCampaign::class, 'campaign_id'); }
    public function reportedBy(): BelongsTo { return $this->belongsTo(Employee::class, 'reported_by_employee_id'); }
    public function technician(): BelongsTo { return $this->belongsTo(Employee::class, 'assigned_technician_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(MaintenanceItem::class); }

    /**
     * Transición de estado con validación.
     */
    public function transitionTo(MaintenanceStatus $newStatus): void
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            throw new \DomainException(
                "No se puede cambiar de '{$this->status->value}' a '{$newStatus->value}'."
            );
        }

        $this->status = $newStatus;

        if ($newStatus === MaintenanceStatus::EN_PROCESO && ! $this->started_at) {
            $this->started_at = now();
        }
        if (in_array($newStatus, [MaintenanceStatus::CERRADO, MaintenanceStatus::CANCELADO])) {
            $this->finished_at = now();
        }

        $this->save();
    }

    /**
     * Recalcular costo total desde ítems.
     */
    public function recalculateCost(): void
    {
        $this->update([
            'total_cost' => $this->items()->sum('total_cost'),
        ]);
    }
}
```

### 10.3. Service: MaintenanceService

```php
// app/Services/MaintenanceService.php
class MaintenanceService
{
    public function createCase(Asset $asset, array $data, User $user): MaintenanceCase
    {
        return DB::transaction(function () use ($asset, $data, $user) {
            $case = MaintenanceCase::create([
                ...$data,
                'asset_id' => $asset->id,
                'created_by' => $user->id,
            ]);

            // Cambiar estado del activo a en_mantenimiento
            if ($asset->status !== AssetStatus::DE_BAJA) {
                $asset->update(['status' => AssetStatus::EN_MANTENIMIENTO->value]);
            }

            return $case;
        });
    }

    public function closeCase(MaintenanceCase $case, array $data): void
    {
        DB::transaction(function () use ($case, $data) {
            $case->update([
                'diagnosis' => $data['diagnosis'] ?? $case->diagnosis,
                'actions_taken' => $data['actions_taken'] ?? $case->actions_taken,
                'conformity_name' => $data['conformity_name'] ?? null,
                'conformity_date' => $data['conformity_date'] ?? now(),
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
            ]);

            $case->transitionTo(MaintenanceStatus::CERRADO);

            // Restaurar estado del activo
            $asset = $case->asset;
            $condition = $data['asset_condition'] ?? $asset->condition;
            $asset->update([
                'status' => AssetStatus::ASIGNADO->value,
                'condition' => $condition,
            ]);
        });
    }
}
```

### 10.4. Service: CampaignService

```php
// app/Services/CampaignService.php
class CampaignService
{
    public function loadAssets(MaintenanceCampaign $campaign, array $assetIds, ?int $technicianId = null): int
    {
        $records = collect($assetIds)->map(fn ($id) => [
            'campaign_id' => $campaign->id,
            'asset_id' => $id,
            'assigned_technician_id' => $technicianId,
            'status' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        return CampaignAsset::upsert($records, ['campaign_id', 'asset_id']);
    }

    public function getProgress(MaintenanceCampaign $campaign): array
    {
        $stats = $campaign->campaignAssets()
            ->selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($stats);

        return [
            'total' => $total,
            'pendiente' => $stats['pendiente'] ?? 0,
            'en_proceso' => $stats['en_proceso'] ?? 0,
            'atendido' => $stats['atendido'] ?? 0,
            'porcentaje' => $total > 0
                ? round((($stats['atendido'] ?? 0) / $total) * 100, 1)
                : 0,
        ];
    }

    public function closeCampaign(MaintenanceCampaign $campaign, string $summary): void
    {
        $campaign->update([
            'status' => CampaignStatus::CERRADA->value,
            'summary' => $summary,
            'metrics_json' => $this->getProgress($campaign),
            'end_date' => now(),
        ]);
    }
}
```

---

## 11. Módulo 5 — Soporte (Detalle)

### 11.1. Generación de documentos PDF

```php
// app/Services/DocumentService.php
class DocumentService
{
    /**
     * Tipos de documento y sus plantillas Blade.
     */
    private array $templates = [
        'ficha_tecnica' => 'documents.templates.ficha-tecnica',
        'acta_mantenimiento' => 'documents.templates.acta-mantenimiento',
        'orden_trabajo' => 'documents.templates.orden-trabajo',
        'constancia_entrega' => 'documents.templates.constancia-entrega',
        'constancia_devolucion' => 'documents.templates.constancia-devolucion',
        'resumen_campana' => 'documents.templates.resumen-campana',
    ];

    public function generate(
        string $documentType,
        string $referenceType,
        int $referenceId,
        User $user
    ): Document {
        $template = $this->templates[$documentType]
            ?? throw new \InvalidArgumentException("Tipo de documento inválido: {$documentType}");

        // Cargar datos según tipo de referencia
        $data = $this->loadData($referenceType, $referenceId);
        $settings = Setting::getGroup('institution');

        // Generar PDF
        $pdf = Pdf::loadView($template, [
            'data' => $data,
            'settings' => $settings,
            'generatedAt' => now(),
        ]);

        // Guardar archivo
        $code = sprintf('DOC-%s-%05d', now()->format('Ymd'), Document::count() + 1);
        $path = "documents/{$referenceType}/{$referenceId}/{$code}.pdf";
        Storage::put($path, $pdf->output());

        return Document::create([
            'document_type' => $documentType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'code' => $code,
            'title' => $this->buildTitle($documentType, $data),
            'file_path' => $path,
            'generated_by' => $user->id,
            'generated_at' => now(),
        ]);
    }

    private function loadData(string $type, int $id): Model
    {
        return match ($type) {
            'asset' => Asset::with(['orgUnit', 'responsible', 'supplier'])->findOrFail($id),
            'maintenance_case' => MaintenanceCase::with(['asset', 'technician', 'items'])->findOrFail($id),
            'campaign' => MaintenanceCampaign::with(['campaignAssets.asset', 'coordinator'])->findOrFail($id),
            'movement' => AssetMovement::with(['asset', 'originUnit', 'destinationUnit'])->findOrFail($id),
            default => throw new \InvalidArgumentException("Referencia inválida: {$type}"),
        };
    }

    private function buildTitle(string $type, Model $data): string
    {
        return match ($type) {
            'ficha_tecnica' => "Ficha Técnica - {$data->internal_code}",
            'acta_mantenimiento' => "Acta de Mantenimiento - {$data->code}",
            'orden_trabajo' => "Orden de Trabajo - {$data->code}",
            'constancia_entrega' => "Constancia de Entrega - {$data->asset->internal_code}",
            'constancia_devolucion' => "Constancia de Devolución - {$data->asset->internal_code}",
            'resumen_campana' => "Resumen de Campaña - {$data->code}",
            default => $type,
        };
    }
}
```

### 11.2. Modelo Setting con cache

```php
// app/Models/Setting.php
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group_name', 'description', 'is_sensitive'];

    protected $casts = ['is_sensitive' => 'boolean'];

    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = cache()->remember(
            "setting:{$key}",
            3600,
            fn () => static::where('key', $key)->first()
        );

        return $setting?->typed_value ?? $default;
    }

    public static function getGroup(string $group): array
    {
        return cache()->remember(
            "settings_group:{$group}",
            3600,
            fn () => static::where('group_name', $group)
                ->pluck('value', 'key')
                ->toArray()
        );
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => cache()->forget("setting:{$s->key}"));
        static::saved(fn ($s) => cache()->forget("settings_group:{$s->group_name}"));
    }
}
```

### 11.3. Reportes

```php
// app/Services/ReportService.php
class ReportService
{
    public function assetsByStatus(): Collection
    {
        return Asset::selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
    }

    public function assetsByType(): Collection
    {
        return Asset::selectRaw("asset_type, count(*) as total")
            ->groupBy('asset_type')
            ->orderByDesc('total')
            ->get();
    }

    public function assetsByUnit(): Collection
    {
        return Asset::join('organizational_units as ou', 'assets.organizational_unit_id', '=', 'ou.id')
            ->selectRaw("ou.name as unit_name, count(*) as total")
            ->groupBy('ou.name')
            ->orderByDesc('total')
            ->get();
    }

    public function maintenanceCostByPeriod(string $from, string $to): Collection
    {
        return MaintenanceCase::whereBetween('finished_at', [$from, $to])
            ->where('status', 'cerrado')
            ->selectRaw("date_trunc('month', finished_at) as month, sum(total_cost) as total")
            ->groupByRaw("date_trunc('month', finished_at)")
            ->orderBy('month')
            ->get();
    }

    public function maintenanceByTechnician(): Collection
    {
        return MaintenanceCase::join('employees as e', 'maintenance_cases.assigned_technician_id', '=', 'e.id')
            ->selectRaw("e.full_name, count(*) as total, sum(case when status='cerrado' then 1 else 0 end) as cerrados")
            ->groupBy('e.full_name')
            ->orderByDesc('total')
            ->get();
    }

    public function assetsWithoutRecentMaintenance(int $days = 180): Collection
    {
        return Asset::whereNotIn('status', ['de_baja'])
            ->where(function ($q) use ($days) {
                $q->whereDoesntHave('maintenanceCases')
                  ->orWhereHas('maintenanceCases', function ($q) use ($days) {
                      $q->havingRaw("max(finished_at) < ?", [now()->subDays($days)]);
                  });
            })
            ->with(['orgUnit', 'responsible'])
            ->get();
    }

    public function unlinkedAgentDevices(): Collection
    {
        return AgentDevice::whereNull('asset_id')
            ->orderByDesc('last_heartbeat_at')
            ->get();
    }
}
```

---

## 12. Seeders y datos iniciales

```php
// database/seeders/DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SettingSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
```

```php
// database/seeders/PermissionSeeder.php
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'assets' => ['view', 'create', 'edit', 'delete', 'movements', 'attachments'],
            'maintenance' => ['view', 'create', 'assign', 'edit', 'close', 'items'],
            'campaigns' => ['view', 'create', 'manage', 'close'],
            'organization' => ['view', 'manage'],
            'users' => ['view', 'manage'],
            'documents' => ['view', 'generate'],
            'reports' => ['view', 'export'],
            'settings' => ['manage'],
            'audit' => ['view'],
            'agents' => ['view', 'manage'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => ucfirst($action) . ' ' . $module,
                    'slug' => "{$module}.{$action}",
                    'module' => $module,
                ]);
            }
        }
    }
}
```

```php
// database/seeders/RoleSeeder.php
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::pluck('id');

        $admin = Role::create([
            'name' => 'Administrador del Sistema',
            'slug' => 'admin',
            'description' => 'Acceso total al sistema',
            'is_system' => true,
        ]);
        $admin->permissions()->attach($allPermissions);

        $adminTI = Role::create([
            'name' => 'Administrador TI',
            'slug' => 'admin_ti',
            'is_system' => true,
        ]);
        $adminTI->permissions()->attach(
            Permission::whereNotIn('module', ['users', 'settings'])->pluck('id')
        );

        $tecnico = Role::create([
            'name' => 'Técnico de Soporte',
            'slug' => 'tecnico',
        ]);
        $tecnico->permissions()->attach(
            Permission::whereIn('slug', [
                'assets.view', 'maintenance.view', 'maintenance.create',
                'maintenance.edit', 'maintenance.items', 'campaigns.view',
                'documents.view', 'documents.generate',
            ])->pluck('id')
        );

        Role::create(['name' => 'Supervisor', 'slug' => 'supervisor']);
        Role::create(['name' => 'Responsable de Oficina', 'slug' => 'responsable']);
        Role::create(['name' => 'Usuario Consulta', 'slug' => 'consulta']);
    }
}
```

```php
// database/seeders/SettingSeeder.php
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'institution_name', 'value' => 'Municipalidad', 'group_name' => 'institution'],
            ['key' => 'institution_ruc', 'value' => '', 'group_name' => 'institution'],
            ['key' => 'institution_address', 'value' => '', 'group_name' => 'institution'],
            ['key' => 'institution_logo_path', 'value' => '', 'group_name' => 'institution'],
            ['key' => 'document_header', 'value' => 'MUNICIPALIDAD', 'group_name' => 'documents'],
            ['key' => 'document_footer', 'value' => 'Sistema SIGAT', 'group_name' => 'documents'],
            ['key' => 'agent_enabled', 'value' => 'false', 'type' => 'boolean', 'group_name' => 'integrations'],
            ['key' => 'ai_enabled', 'value' => 'false', 'type' => 'boolean', 'group_name' => 'integrations'],
            ['key' => 'ai_provider', 'value' => 'anthropic', 'group_name' => 'integrations'],
        ];

        foreach ($settings as $s) {
            Setting::create(array_merge([
                'type' => 'string',
                'is_sensitive' => false,
            ], $s));
        }
    }
}
```

---

## 13. API Endpoints

### 13.1. Rutas web principales

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    // Organización
    Route::resource('org-units', OrgUnitController::class)->middleware('perm:organization.view');
    Route::resource('employees', EmployeeController::class)->middleware('perm:organization.view');
    Route::resource('suppliers', SupplierController::class)->middleware('perm:organization.view');

    // Activos
    Route::resource('assets', AssetController::class)->middleware('perm:assets.view');
    Route::post('assets/{asset}/movements', [AssetMovementController::class, 'store'])
         ->middleware('perm:assets.movements')->name('assets.movements.store');

    // Mantenimiento
    Route::resource('maintenance', MaintenanceCaseController::class)->middleware('perm:maintenance.view');
    Route::post('maintenance/{case}/transition', [MaintenanceCaseController::class, 'transition'])
         ->middleware('perm:maintenance.edit')->name('maintenance.transition');
    Route::resource('maintenance.items', MaintenanceItemController::class)
         ->shallow()->middleware('perm:maintenance.items');

    // Campañas
    Route::resource('campaigns', CampaignController::class)->middleware('perm:campaigns.view');
    Route::post('campaigns/{campaign}/load-assets', [CampaignController::class, 'loadAssets'])
         ->middleware('perm:campaigns.manage')->name('campaigns.load-assets');
    Route::post('campaigns/{campaign}/close', [CampaignController::class, 'close'])
         ->middleware('perm:campaigns.close')->name('campaigns.close');

    // Documentos
    Route::post('documents/generate', [DocumentController::class, 'generate'])
         ->middleware('perm:documents.generate')->name('documents.generate');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
         ->middleware('perm:documents.view')->name('documents.download');

    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->middleware('perm:reports.view');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])
         ->middleware('perm:reports.export');

    // Admin
    Route::resource('users', UserController::class)->middleware('perm:users.manage');
    Route::resource('roles', RoleController::class)->middleware('perm:users.manage');
    Route::get('settings', [SettingController::class, 'index'])->middleware('perm:settings.manage');
    Route::put('settings', [SettingController::class, 'update'])->middleware('perm:settings.manage');
    Route::get('audit', [AuditController::class, 'index'])->middleware('perm:audit.view');
});
```

### 13.2. API para agente local

```php
// routes/api.php
Route::prefix('agent/v1')->middleware('auth:sanctum')->group(function () {
    Route::post('register', [AgentController::class, 'register']);
    Route::post('heartbeat', [AgentController::class, 'heartbeat']);
    Route::post('sync', [AgentController::class, 'sync']);
});
```

---

## 14. Validaciones clave

```php
// app/Http/Requests/StoreAssetRequest.php
class StoreAssetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'internal_code' => ['required', 'string', 'max:50', 'unique:assets'],
            'patrimonial_code' => ['nullable', 'string', 'max:50', 'unique:assets'],
            'name' => ['required', 'string', 'max:255'],
            'asset_type' => ['required', Rule::enum(AssetType::class)],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(AssetStatus::class)],
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'responsible_employee_id' => ['nullable', 'exists:employees,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'reference_value' => ['nullable', 'numeric', 'min:0'],
            'specs_json' => ['nullable', 'array'],
            'extra_json' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
```

---

## 15. Políticas de autorización

```php
// app/Policies/AssetPolicy.php
class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('assets.view');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('assets.view');
    }

    public function create(User $user): bool
    {
        return $user->can('assets.create');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->can('assets.edit');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->can('assets.delete');
    }
}
```

---

## 16. Testing

### Estrategia

Usar **Pest PHP** con factories para cada modelo.

```php
// tests/Feature/AssetTest.php
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    $this->admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
});

it('can create an asset', function () {
    $data = Asset::factory()->raw();

    $this->actingAs($this->admin)
         ->post(route('assets.store'), $data)
         ->assertRedirect();

    $this->assertDatabaseHas('assets', ['internal_code' => $data['internal_code']]);
});

it('validates required fields', function () {
    $this->actingAs($this->admin)
         ->post(route('assets.store'), [])
         ->assertSessionHasErrors(['internal_code', 'name', 'asset_type']);
});

it('registers a movement and updates asset', function () {
    $asset = Asset::factory()->create(['status' => 'disponible']);
    $employee = Employee::factory()->create();
    $unit = OrgUnit::factory()->create();

    $this->actingAs($this->admin)
         ->post(route('assets.movements.store', $asset), [
             'movement_type' => 'asignacion',
             'to_employee_id' => $employee->id,
             'destination_unit_id' => $unit->id,
             'movement_date' => now()->toDateString(),
         ])
         ->assertRedirect();

    $asset->refresh();
    expect($asset->status->value)->toBe('asignado')
        ->and($asset->responsible_employee_id)->toBe($employee->id);
});

it('prevents unauthorized access', function () {
    $consulta = User::factory()->create([
        'role_id' => Role::where('slug', 'consulta')->first()->id,
    ]);

    $this->actingAs($consulta)
         ->post(route('assets.store'), Asset::factory()->raw())
         ->assertForbidden();
});
```

---

## 17. Despliegue

### 17.1. Requisitos del servidor

```
Ubuntu 22.04+ o similar
PHP 8.2+ (con extensiones: pdo_pgsql, mbstring, xml, zip, gd, redis)
Nginx
PostgreSQL 16
Redis 7
Supervisor (para queues)
Certbot (SSL)
```

### 17.2. Checklist de deploy

```bash
# 1. Clonar y configurar
git clone <repo> /var/www/sigat
cd /var/www/sigat
cp .env.example .env
# Editar .env con credenciales reales

# 2. Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Configurar aplicación
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 5. Supervisor para queues
# /etc/supervisor/conf.d/sigat-worker.conf
# [program:sigat-worker]
# command=php /var/www/sigat/artisan queue:work redis --sleep=3 --tries=3

# 6. Cron para scheduler
# * * * * * cd /var/www/sigat && php artisan schedule:run >> /dev/null 2>&1
```

---

## 18. Roadmap post-MVP

| Fase | Funcionalidad | Prioridad |
|------|--------------|-----------|
| 3 | Agente local (registro, heartbeat, snapshots) | Media |
| 3 | IA asistida (diagnósticos, redacción, consultas) | Media |
| 4 | Dashboard avanzado con gráficos interactivos | Alta |
| 4 | Notificaciones por email con templates | Media |
| 4 | Permisos por unidad organizacional | Media |
| 4 | Importación masiva de activos (Excel) | Alta |
| 4 | Exportación de activos con códigos QR | Baja |
| 4 | Firma digital en documentos | Baja |
| 4 | App móvil para técnicos (PWA) | Media |
| 4 | API REST completa para integraciones externas | Baja |

---

## Resumen de tablas finales del MVP

| # | Tabla | Módulo |
|---|-------|--------|
| 1 | roles | Seguridad |
| 2 | permissions | Seguridad |
| 3 | role_permission | Seguridad |
| 4 | users | Seguridad |
| 5 | organizational_units | Organización |
| 6 | employees | Organización |
| 7 | suppliers | Organización |
| 8 | assets | Activos |
| 9 | asset_movements | Activos |
| 10 | media (spatie) | Activos |
| 11 | maintenance_campaigns | Mantenimiento |
| 12 | maintenance_cases | Mantenimiento |
| 13 | maintenance_items | Mantenimiento |
| 14 | campaign_assets | Mantenimiento |
| 15 | documents | Soporte |
| 16 | settings | Soporte |
| 17 | notifications | Soporte |
| 18 | activity_log (spatie) | Soporte |
| 19 | agent_devices | Soporte (Fase 3) |
| 20 | agent_syncs | Soporte (Fase 3) |
| 21 | ai_logs | Soporte (Fase 3) |

**Total: 21 tablas** (19 originales + permissions + role_permission; se reemplazan attachments y activity_logs manuales por paquetes spatie).