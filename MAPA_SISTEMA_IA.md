# Mapa del sistema para desarrollo asistido con IA

Fecha de revision: 2026-04-28

Este documento resume lo que ya esta implementado en el sistema de mantenimiento y activos MDSJ. Su objetivo es servir como contexto base para continuar agregando funcionalidades con ayuda de agentes de IA, evitando que cada agente tenga que redescubrir toda la arquitectura desde cero.

## Resumen general

El proyecto es una aplicacion Laravel para gestion de activos tecnologicos, mantenimiento, documentos, usuarios, permisos y agentes de monitoreo.

Actualmente el sistema incluye:

- Autenticacion web con usuarios activos/inactivos.
- Gestion de usuarios, roles y permisos con Spatie Permission.
- Gestion de empleados y unidades organizacionales.
- Inventario de activos TI.
- Movimientos de activos.
- Campanas de mantenimiento.
- Casos de mantenimiento con items, cierre, plantillas e informes.
- Plantillas reutilizables de mantenimiento.
- Panel operativo para tecnicos.
- Generacion y descarga de documentos PDF.
- API para agentes instalados en equipos.
- Dashboard, sidebar, topbar y componentes Blade reutilizables.

## Stack tecnico

Backend:

- PHP `^8.3`
- Laravel `^13.0`
- `spatie/laravel-permission` para roles y permisos.
- `barryvdh/laravel-dompdf` para PDFs.
- PHPUnit `^12.5.12`.
- Laravel Pint disponible para formato.

Frontend:

- Blade.
- Tailwind CSS 4.
- Alpine.js 3.
- Vite.

Archivos principales:

- `composer.json`
- `package.json`
- `vite.config.js`
- `resources/css/app.css`
- `resources/js/app.js`

## Estructura principal de carpetas

```text
app/
  Actions/                 Acciones de negocio puntuales.
  DTOs/                    Objetos de transferencia de datos.
  Enums/                   Catalogos tipados del dominio.
  Exceptions/              Excepciones especificas por modulo.
  Http/
    Controllers/           Controladores web y API.
    Middleware/            Middlewares propios.
    Requests/              FormRequests de validacion/autorizacion.
  Models/                  Modelos Eloquent.
  Providers/               Providers de Laravel.
  Services/                Servicios de aplicacion por modulo.

config/
  navigation.php           Sidebar y command palette.
  permission.php           Configuracion de Spatie Permission.

database/
  migrations/              Esquema de base de datos.
  seeders/                 Datos iniciales y de prueba.

resources/
  views/                   Vistas Blade.
  views/components/        Componentes Blade reutilizables.
  css/app.css              Estilos Tailwind.
  js/app.js                Bootstrap JS y Alpine.

routes/
  web.php                  Rutas web autenticadas.
  api.php                  API v1 para agentes y busquedas.
  console.php              Comandos de consola.
```

## Patron de arquitectura usado

La aplicacion usa una separacion bastante clara:

- `Controller`: recibe request, llama servicios/acciones y retorna vista, redirect o JSON.
- `FormRequest`: valida datos y suele verificar permisos con `authorize()`.
- `DTO`: normaliza datos de entrada antes de llegar a acciones.
- `Service`: agrupa consultas y casos de uso de un modulo.
- `Action`: ejecuta operaciones concretas como crear, actualizar o cerrar.
- `Model`: define fillables, casts, relaciones y scopes.
- `Enum`: centraliza valores validos y etiquetas para UI.
- `Blade`: renderiza interfaz web.

Recomendacion para agentes IA: antes de implementar una funcionalidad nueva, buscar si ya existe un modulo similar y copiar su patron. Por ejemplo, para un CRUD nuevo, revisar `AssetController`, `StoreAssetRequest`, `AssetService`, DTOs y vistas de `resources/views/assets`.

## Rutas implementadas

`php artisan route:list` muestra 111 rutas.

Rutas web principales:

- `/login`, `/logout`
- `/` dashboard
- `/users`
- `/employees`
- `/organizational-units`
- `/assets`
- `/asset-movements`
- `/campaigns`
- `/maintenance-cases`
- `/maintenance-templates`
- `/documents`
- `/agents`
- `/settings`
- `/admin/roles`
- `/admin/permissions`
- `/tecnico`
- `/tecnico/cola`
- `/tecnico/atender`
- `/tecnico/casos/{maintenanceCase}`

Rutas API bajo `api/v1`:

- `POST agents/register`
- `POST agents/{uuid}/heartbeat`
- `POST agents/{uuid}/sync`
- `POST agents/{uuid}/snapshot`
- `POST agents/{uuid}/bind-asset`
- `GET organizational-units`
- `GET employees/search`

Archivos:

- `routes/web.php`
- `routes/api.php`

## Seguridad y middlewares

Middlewares registrados en `bootstrap/app.php`:

- `user.active`: bloquea usuarios inactivos.
- `role`: middleware de Spatie.
- `permission`: middleware de Spatie.
- `role_or_permission`: middleware de Spatie.
- `agent.auth`: autentica agentes por token.

El grupo privado de `routes/web.php` usa:

- `auth`
- `user.active`

Roles principales:

- `admin`
- `tecnico`
- `empleado`
- `responsable_oficina`

Permisos principales:

- `user.*`
- `employee.*`
- `org-unit.*`
- `asset.*`
- `asset-movement.*`
- `campaign.*`
- `maintenance-case.*`
- `maintenance-template.*`
- `document.*`
- `setting.*`
- `agent.*`

Archivos relevantes:

- `app/Enums/RoleEnum.php`
- `app/Enums/PermissionEnum.php`
- `database/seeders/RolePermissionSeeder.php`
- `app/Http/Middleware/CheckUserIsActive.php`
- `app/Http/Middleware/AuthenticateAgent.php`
- `app/Providers/AppServiceProvider.php`

## Modelos implementados

### User

Archivo: `app/Models/User.php`

Representa usuarios del sistema.

Relaciones y comportamiento:

- Usa autenticacion Laravel.
- Usa roles y permisos de Spatie.
- Pertenece a un empleado mediante `employee()`.
- Scopes: `search`, `byRole`, `active`, `inactive`.
- Helper: `isAdmin()`.

Campos importantes:

- `employee_id`
- `username`
- `email`
- `password`
- `is_active`
- `last_login_at`

### Role y Permission

Archivos:

- `app/Models/Role.php`
- `app/Models/Permission.php`

Extienden modelos de Spatie:

- `Spatie\Permission\Models\Role`
- `Spatie\Permission\Models\Permission`

Se usan para administracion granular desde `/admin/roles` y `/admin/permissions`.

### OrganizationalUnit

Archivo: `app/Models/OrganizationalUnit.php`

Representa sedes, gerencias, subgerencias, oficinas y otras unidades.

Relaciones:

- `parent()`: unidad padre.
- `children()`: unidades hijas.
- `responsible()`: empleado responsable.
- `assets()`: activos asociados.
- `empoyees()`: empleados asociados. Nota: el nombre del metodo tiene typo; probablemente deberia ser `employees()`.

Helper:

- `full_path`: ruta jerarquica completa.

### Employee

Archivo: `app/Models/Employee.php`

Representa trabajadores y tecnicos.

Relaciones:

- `organizationalUnit()`
- `user()`
- `assignedAssets()`
- `maintenanceCases()`
- `reportedCases()`
- `coordinatedCampaigns()`

Scopes:

- `technicians`
- `active`
- `search`

Campos importantes:

- `dni`
- `name`
- `last_name`
- `full_name`
- `email`
- `phone`
- `position`
- `organizational_unit_id`
- `is_technician`
- `specialty`
- `level`
- `is_active`

### Asset

Archivo: `app/Models/Asset.php`

Representa activos tecnologicos inventariados.

Relaciones:

- `organizationalUnit()`
- `responsible()`
- `movements()`
- `maintenanceCases()`
- `campaignAssets()`
- `agentDevice()`
- `documents()`

Casts:

- `asset_type` a `AssetType`
- `status` a `AssetStatus`
- `condition` a `AssetCondition`
- `purchase_date` a date
- `reference_value` a decimal
- `specs_json` y `extra_json` a array

Scopes:

- `search`
- `byType`
- `byStatus`
- `byUnit`

Usa `SoftDeletes` y genera `uuid` al crear.

### AssetMovement

Archivo: `app/Models/AssetMovement.php`

Registra asignaciones, traslados, devoluciones, bajas, ingresos y prestamos.

Relaciones:

- `asset()`
- `originUnit()`
- `destinationUnit()`
- `fromEmployee()`
- `toEmployee()`
- `createdBy()`

### MaintenanceCampaign

Archivo: `app/Models/MaintenanceCampaign.php`

Representa campanas preventivas o masivas.

Relaciones:

- `coordinator()`
- `createdBy()`
- `campaignAssets()`
- `assets()` mediante tabla pivot `campaign_assets`.
- `maintenanceCases()`

Scopes:

- `search`
- `byStatus`

### CampaignAsset

Archivo: `app/Models/CampaignAsset.php`

Une una campana con un activo.

Relaciones:

- `campaign()`
- `asset()`
- `assignedTechnician()`
- `maintenanceCase()`

### MaintenanceCase

Archivo: `app/Models/MaintenanceCase.php`

Representa un caso de mantenimiento.

Relaciones:

- `asset()`
- `campaign()`
- `reportedBy()`
- `assignedTechnician()`
- `createdBy()`
- `items()`
- `documents()`

Casts:

- `maintenance_type` a `MaintenanceType`
- `priority` a `MaintenancePriority`
- `status` a `MaintenanceCaseStatus`
- fechas y costo total tipados

Scopes:

- `search`
- `byStatus`
- `byType`
- `byTechnician`

### MaintenanceItem

Archivo: `app/Models/MaintenanceItem.php`

Representa repuestos, insumos, servicios o herramientas asociados a un caso.

Relacion:

- `maintenanceCase()`

Campos importantes:

- `maintenance_case_id`
- `item_type`
- `name`
- `description`
- `quantity`
- `unit_cost`
- `total_cost`
- `data_json`

### MaintenanceTemplate

Archivo: `app/Models/MaintenanceTemplate.php`

Plantilla reutilizable para iniciar o completar casos de mantenimiento.

Relaciones:

- `items()`
- `createdBy()`

Scopes:

- `active`
- `byType`

Metodo:

- `toApiArray()`: serializa plantilla e items para respuestas JSON.

Campos importantes:

- `name`
- `maintenance_type`
- `asset_type`
- `description`
- `problem_description`
- `diagnosis_template`
- `actions_template`
- `steps_json`
- `next_maintenance_interval_days`
- `is_active`
- `created_by`

### MaintenanceTemplateItem

Archivo: `app/Models/MaintenanceTemplateItem.php`

Item predefinido dentro de una plantilla.

Relacion:

- `template()`

Campos:

- `maintenance_template_id`
- `item_type`
- `name`
- `description`
- `quantity`
- `unit_cost`
- `sort_order`

### Document

Archivo: `app/Models/Document.php`

Registra documentos generados o cargados.

Relaciones:

- `reference()`: relacion polimorfica.
- `generatedBy()`

Scope:

- `byType`

Tipos usados:

- ficha tecnica
- historial de mantenimiento
- informe tecnico
- acta de mantenimiento
- inventario
- otros

### Setting

Archivo: `app/Models/Setting.php`

Configuracion editable del sistema.

Caracteristicas:

- Scope `byGroup`.
- Metodo `getTypedValue()`.
- Metodo estatico `Setting::get($key, $default)`.

Tipos:

- string
- integer
- boolean
- json
- text

### AgentDevice

Archivo: `app/Models/AgentDevice.php`

Representa un agente instalado en un equipo.

Relaciones:

- `asset()`
- `syncs()`
- `lastSync()`

Scope:

- `byStatus`

Metodo:

- `isOnline()`

Genera token si no existe al crear.

### AgentSync

Archivo: `app/Models/AgentSync.php`

Registra eventos de sincronizacion de agentes.

Relacion:

- `agentDevice()`

### AiLog

Archivo: `app/Models/AiLog.php`

Registra interacciones o acciones asistidas por IA.

Relaciones:

- `user()`
- `context()`: polimorfica

Actualmente existe la tabla y modelo, pero no se observa un flujo web completo usando este log.

## Enums disponibles

Ubicacion: `app/Enums`

- `AgentDeviceStatus`: `activo`, `inactivo`, `desconectado`
- `AgentSyncStatus`: `recibido`, `procesado`, `error`
- `AgentSyncType`: `heartbeat`, `snapshot`, `delta`
- `AiLogStatus`: `pendiente`, `exitoso`, `error`
- `AssetCondition`: `bueno`, `regular`, `malo`, `obsoleto`
- `AssetStatus`: `activo`, `en_uso`, `en_almacen`, `en_reparacion`, `dado_de_baja`, `extraviado`
- `AssetType`: computadora, laptop, servidor, impresora, escaner, monitor, tablet, telefono IP, proyector, UPS, red, camara, disco externo, otro
- `CampaignAssetStatus`: `pendiente`, `programado`, `atendido`, `omitido`
- `CampaignStatus`: `planificada`, `en_curso`, `pausada`, `completada`, `cancelada`
- `DocumentType`: actas, ordenes, informes, ficha tecnica, historial, inventario, otro
- `MaintenanceCaseStatus`: `pendiente`, `en_progreso`, `en_espera`, `completado`, `cancelado`
- `MaintenanceItemType`: `repuesto`, `insumo`, `servicio`, `herramienta`
- `MaintenancePriority`: `baja`, `media`, `alta`, `critica`
- `MaintenanceType`: `preventivo`, `correctivo`, `predictivo`, `emergencia`
- `MovementType`: `asignacion`, `traslado`, `devolucion`, `baja`, `ingreso`, `prestamo`
- `OrgUnitType`: `gerencia`, `oficina_general`, `subgerencia`, `oficina`, `sede`
- `PermissionEnum`: permisos granulares por modulo
- `RoleEnum`: roles principales
- `SettingType`: tipos de configuracion

## Modulos web implementados

### Autenticacion

Archivos:

- `app/Http/Controllers/Auth/AuthController.php`
- `app/Services/Auth/AuthService.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `resources/views/auth/login.blade.php`

Incluye:

- Login por usuario o email.
- Validacion de credenciales.
- Bloqueo de usuario inactivo.
- Logout.

### Dashboard

Archivos:

- `app/Http/Controllers/DashboardController.php`
- `resources/views/dashboard.blade.php`
- `resources/views/pages/dashboard.blade.php`

Muestra metricas generales de activos, mantenimiento, campanas y tecnicos.

### Usuarios

Archivos:

- `app/Http/Controllers/UserController.php`
- `app/Services/User/UserService.php`
- `app/Actions/User/CreateUserAction.php`
- `app/Actions/User/UpdateUserAction.php`
- `app/DTOs/User/CreateUserDTO.php`
- `app/DTOs/User/UpdateUserDTO.php`
- `resources/views/users`

Incluye:

- Listado.
- Crear.
- Editar.
- Eliminar.
- Activar/desactivar.
- Asignar roles.

### Roles y permisos

Archivos:

- `app/Http/Controllers/Role/RoleController.php`
- `app/Http/Controllers/Permission/PermissionController.php`
- `app/Services/Role/RoleService.php`
- `app/Services/Permission/PermissionService.php`
- `resources/views/roles`
- `resources/views/permissions`

Rutas bajo `/admin`, protegidas por rol `admin`.

### Empleados

Archivos:

- `app/Http/Controllers/EmployeeController.php`
- `app/Services/Employee/EmployeeService.php`
- `app/DTOs/Employee`
- `app/Http/Requests/Employee`
- `resources/views/employees`

Incluye CRUD, busqueda, estado activo e identificacion de tecnicos.

### Unidades organizacionales

Archivos:

- `app/Http/Controllers/OrganizationalUnitController.php`
- `app/Services/OrganizationalUnit/OrganizationalUnitService.php`
- `app/DTOs/OrganizationalUnitDTO.php`
- `resources/views/organizational-units`

Incluye estructura jerarquica, unidades padre/hijas y responsables.

### Activos

Archivos:

- `app/Http/Controllers/AssetController.php`
- `app/Services/Asset/AssetService.php`
- `app/Actions/Asset/CreateAssetAction.php`
- `app/Actions/Asset/UpdateAssetAction.php`
- `app/DTOs/Asset`
- `resources/views/assets`

Incluye:

- CRUD completo.
- Busqueda y filtros por tipo, estado y unidad.
- Especificaciones JSON.
- Documentos generados: ficha tecnica e historial.
- Relacion con responsable, unidad, movimientos, agente y casos.

### Movimientos de activos

Archivos:

- `app/Http/Controllers/AssetMovementController.php`
- `app/Services/AssetMovement/AssetMovementService.php`
- `app/Actions/AssetMovement/CreateMovementAction.php`
- `app/DTOs/AssetMovement/CreateAssetMovementDTO.php`
- `resources/views/asset-movements`

Incluye:

- Listado.
- Registro de movimiento.
- Actualizacion de ubicacion/responsable del activo segun movimiento.

### Campanas de mantenimiento

Archivos:

- `app/Http/Controllers/MaintenanceCampaignController.php`
- `app/Services/MaintenanceCampaign/MaintenanceCampaignService.php`
- `app/Actions/MaintenanceCampaign`
- `app/DTOs/MaintenanceCampaign`
- `resources/views/campaigns`

Incluye:

- CRUD.
- Asociar activos a campana.
- Quitar activos de campana.
- Agregar activos por unidad.
- Crear casos masivamente.
- Generar acta PDF.

### Casos de mantenimiento

Archivos:

- `app/Http/Controllers/MaintenanceCaseController.php`
- `app/Services/MaintenanceCase/MaintenanceCaseService.php`
- `app/Actions/MaintenanceCase/CreateCaseAction.php`
- `app/Actions/MaintenanceCase/UpdateCaseAction.php`
- `app/Actions/MaintenanceCase/CloseCaseAction.php`
- `app/DTOs/MaintenanceCase`
- `app/DTOs/MaintenanceItem`
- `resources/views/maintenance-cases`

Incluye:

- CRUD completo.
- Asignacion de activo y tecnico.
- Estados: pendiente, en progreso, en espera, completado, cancelado.
- Items de mantenimiento.
- Aplicacion de plantillas.
- Cierre de caso.
- Generacion de informe tecnico.

### Plantillas de mantenimiento

Archivos:

- `app/Http/Controllers/MaintenanceTemplateController.php`
- `app/Services/MaintenanceTemplate/MaintenanceTemplateService.php`
- `app/Models/MaintenanceTemplate.php`
- `app/Models/MaintenanceTemplateItem.php`
- `resources/views/maintenance-templates`

Incluye:

- Listado.
- Crear.
- Editar.
- Eliminar.
- Items predefinidos.
- Datos JSON para aplicar una plantilla.
- Listado filtrado por tipo.

### Panel tecnico

Archivos:

- `app/Http/Controllers/TechnicianController.php`
- `app/Services/Technician/TechnicianService.php`
- `resources/views/technicians`

Incluye:

- Dashboard del tecnico.
- Cola de trabajo.
- Atencion rapida de activo.
- Flujo de caso.
- Actualizacion de progreso.
- Agregar/quitar items.
- Aplicar plantillas.
- Cerrar caso.

Rutas principales:

- `tecnico.dashboard`
- `tecnico.work-queue`
- `tecnico.attend-asset`
- `tecnico.cases.workflow`
- `tecnico.cases.show`
- `tecnico.cases.progress`
- `tecnico.cases.apply-template`
- `tecnico.cases.close`

### Documentos

Archivos:

- `app/Http/Controllers/DocumentController.php`
- `app/Services/Document/DocumentService.php`
- `app/Services/Document/DocumentGeneratorService.php`
- `resources/views/documents`
- `resources/views/documents/pdf`

Plantillas PDF:

- `acta-mantenimiento.blade.php`
- `ficha-tecnica.blade.php`
- `historial-mantenimiento.blade.php`
- `informe-tecnico.blade.php`

Incluye:

- Listado de documentos.
- Crear/registrar documento.
- Descargar.
- Eliminar.
- Generacion PDF para activos, casos y campanas.

### Agentes de monitoreo

Archivos:

- `app/Http/Controllers/AgentDeviceController.php`
- `app/Services/AgentDevice/AgentDeviceService.php`
- `app/Http/Requests/Agent`
- `resources/views/agents`

Incluye:

- Registro de agente con clave de instalacion.
- Heartbeat.
- Sync.
- Snapshot.
- Vinculacion con activo.
- Vista web de agentes.

La autenticacion API usa Bearer token asociado a `agent_devices.api_token`.

### Configuracion

Archivos:

- `app/Http/Controllers/SettingController.php`
- `app/Services/Setting/SettingService.php`
- `app/DTOs/Setting/UpdateSettingDTO.php`
- `resources/views/settings/index.blade.php`

Incluye:

- Listado por grupos.
- Actualizacion de valores.
- Tipos con conversion a valor real.

## Componentes Blade reutilizables

Ubicacion: `resources/views/components`

Layouts:

- `layouts/app.blade.php`
- `layouts/auth.blade.php`
- `layouts/partials/head.blade.php`
- `layouts/partials/sidebar.blade.php`
- `layouts/partials/topbar.blade.php`
- `layouts/partials/command-palette.blade.php`

Componentes UI:

- `avatar`
- `badge`
- `button`
- `card`
- `checkbox`
- `data-table`
- `dropdown`
- `dropdown-item`
- `empty-state`
- `flash`
- `input`
- `modal`
- `page-header`
- `search-input`
- `searchable-select`
- `select`
- `specs-builder`
- `stat-card`
- `tab-panel`
- `tabs`
- `td`
- `textarea`
- `th`
- `toggle`

Iconos:

- `building`
- `clipboard`
- `cog`
- `cube`
- `document`
- `squares`
- `users`
- `wrench`

Navegacion:

- `config/navigation.php` define modulos del sidebar, submenu tecnico y acciones de command palette.

## Base de datos

Migraciones principales:

- `users`, `password_reset_tokens`, `sessions`
- `cache`, `cache_locks`
- `jobs`, `job_batches`, `failed_jobs`
- tablas de Spatie Permission
- `organizational_units`
- `employees`
- `assets`
- `asset_movements`
- `maintenance_campaigns`
- `maintenance_cases`
- `maintenance_items`
- `campaign_assets`
- `documents`
- `settings`
- `notifications`
- `agent_devices`
- `agent_syncs`
- `ai_logs`
- `maintenance_templates`
- `maintenance_template_items`

Seeders:

- `SuperAdminSeeder`
- `UserSeeder`
- `RolePermissionSeeder`
- `OrganizationalUnitSeeder`
- `EmployeeSeeder`
- `AssetSeeder`
- `AssetMovementSeeder`
- `MaintenanceSeeder`
- `MaintenanceTemplateSeeder`
- `DocumentSeeder`
- `SettingSeeder`
- `AgentSeeder`

Seeder raiz:

- `database/seeders/DatabaseSeeder.php`

## Flujos de negocio importantes

### Crear un activo

Flujo esperado:

1. Ruta `assets.create` muestra formulario.
2. `StoreAssetRequest` valida.
3. `AssetController@store` llama servicio/accion.
4. `CreateAssetAction` crea el activo.
5. El modelo `Asset` genera `uuid`.

### Registrar movimiento

Flujo esperado:

1. `AssetMovementController@store`.
2. `StoreAssetMovementRequest` valida.
3. `AssetMovementService` / `CreateMovementAction` registra movimiento.
4. Se actualiza responsable o unidad del activo segun corresponda.

### Crear caso de mantenimiento

Flujo esperado:

1. `MaintenanceCaseController@store`.
2. `StoreMaintenanceCaseRequest` valida.
3. DTO `CreateMaintenanceCaseDTO`.
4. `CreateCaseAction` crea caso.
5. Se pueden agregar items.
6. Se puede aplicar plantilla.
7. Se puede cerrar con `CloseCaseAction`.

### Aplicar plantilla de mantenimiento

Flujo esperado:

1. Usuario elige plantilla en caso o panel tecnico.
2. Se consulta `maintenance-templates/{id}/data` o `maintenance-templates-list`.
3. Se cargan textos base, pasos e items.
4. Se aplican al caso mediante rutas `apply-template`.

### Generar documento PDF

Flujo esperado:

1. Controlador llama `DocumentGeneratorService`.
2. Se renderiza una vista Blade PDF.
3. Se guarda archivo.
4. Se crea registro en `documents`.
5. Usuario descarga desde modulo documentos o desde entidad relacionada.

### Agente de monitoreo

Flujo esperado:

1. Agente se registra con `POST api/v1/agents/register`.
2. Recibe/usa token.
3. Envia heartbeat, snapshot o sync.
4. `AuthenticateAgent` valida Bearer token.
5. `AgentDeviceService` actualiza estado, ultimo heartbeat, snapshots y sincronizaciones.

## Comandos utiles

Instalacion:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

Desarrollo:

```bash
composer run dev
```

Servidor Laravel solamente:

```bash
php artisan serve
```

Vite solamente:

```bash
npm run dev
```

Tests:

```bash
composer test
php artisan test
```

Formato:

```bash
vendor/bin/pint
```

Rutas:

```bash
php artisan route:list
```

## Reglas para pedir trabajo a agentes IA

Cuando se delegue una nueva funcionalidad, conviene pasar este documento y pedir que el agente respete estas reglas:

- Revisar primero modelos, requests, services, actions y vistas del modulo mas parecido.
- No modificar archivos no relacionados.
- No cambiar nombres de rutas existentes si no es estrictamente necesario.
- Mantener permisos con `PermissionEnum`, seeders y middlewares.
- Usar FormRequests para validacion.
- Usar DTOs y Actions si el modulo existente ya los usa.
- Usar Enums para estados/tipos en vez de strings sueltos.
- Mantener Blade + Tailwind + Alpine, sin introducir frameworks nuevos.
- Si se agrega una tabla, crear migracion, modelo, relaciones y seeder si aplica.
- Si se agrega un menu, actualizar `config/navigation.php`.
- Si se agrega permiso, actualizar `PermissionEnum` y `RolePermissionSeeder`.
- Si se agrega PDF, crear vista en `resources/views/documents/pdf` y registrar documento con `DocumentService`.
- Ejecutar como minimo `php artisan test` o justificar si no se pudo.

## Checklist para agregar un nuevo modulo

1. Crear migracion.
2. Crear modelo con `fillable`, `casts`, relaciones y scopes.
3. Crear enum si hay estados o tipos.
4. Crear DTOs si hay operaciones de creacion/actualizacion.
5. Crear FormRequests.
6. Crear Service.
7. Crear Actions para operaciones de negocio importantes.
8. Crear Controller.
9. Agregar rutas en `routes/web.php` o `routes/api.php`.
10. Agregar permisos en `PermissionEnum`.
11. Actualizar `RolePermissionSeeder`.
12. Agregar vistas Blade.
13. Agregar entrada en `config/navigation.php` si debe aparecer en UI.
14. Agregar seeder si se requieren datos iniciales.
15. Probar rutas y permisos.

## Observaciones y posibles mejoras detectadas

- `OrganizationalUnit::empoyees()` parece tener un typo; evaluar renombrarlo a `employees()` y revisar usos.
- Hay documentacion previa en `ESTADO_IMPLEMENTACION.md`, pero esta guia esta mas actualizada al 2026-04-28 e incluye plantillas y panel tecnico.
- Existen cambios sin commitear en varios archivos, incluyendo plantillas de mantenimiento y panel tecnico. Antes de cambios grandes, revisar `git status`.
- Algunos comentarios o textos antiguos aparecen con caracteres mal codificados en ciertos archivos; si se editan, conviene normalizar codificacion con cuidado.
- `AiLog` existe a nivel de tabla/modelo, pero falta confirmar si ya hay flujos completos de UI o servicios que lo usen.

## Archivos clave para una IA antes de implementar

Leer primero:

- `routes/web.php`
- `routes/api.php`
- `config/navigation.php`
- `app/Enums/PermissionEnum.php`
- `app/Enums/RoleEnum.php`
- `database/seeders/RolePermissionSeeder.php`
- `app/Models`
- el controller/service/request del modulo mas parecido a la tarea

Para UI:

- `resources/views/components/layouts/app.blade.php`
- `resources/views/components/layouts/partials/sidebar.blade.php`
- `resources/views/components/ui`
- vistas del modulo mas parecido

Para PDFs:

- `app/Services/Document/DocumentGeneratorService.php`
- `resources/views/documents/pdf`

Para API de agentes:

- `routes/api.php`
- `app/Http/Controllers/AgentDeviceController.php`
- `app/Services/AgentDevice/AgentDeviceService.php`
- `app/Http/Middleware/AuthenticateAgent.php`

