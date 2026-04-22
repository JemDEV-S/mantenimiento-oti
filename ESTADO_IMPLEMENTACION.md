# Estado Actual del Proyecto Laravel

Fecha de revisión: 2026-04-20

## 1. Resumen ejecutivo

El proyecto ya tiene una base funcional sólida para un sistema de mantenimiento y gestión de activos TI de la MDSJ. No es un esqueleto vacío: ya existen módulos web completos, API para agentes, modelos con relaciones, validaciones por `FormRequest`, servicios, acciones de negocio, seeders con datos de prueba y una interfaz Blade con Tailwind + Alpine.

En términos prácticos, hoy el sistema ya cubre:

- Autenticación web.
- Usuarios, roles y permisos.
- Empleados.
- Unidades organizacionales.
- Inventario de activos.
- Movimientos de activos.
- Campañas de mantenimiento.
- Casos de mantenimiento e ítems.
- Documentos.
- Configuración.
- Agentes de monitoreo con endpoints API.
- Dashboard con métricas.

También hay varios detalles importantes a tener en cuenta antes de continuar implementación:

- Hay funcionalidades documentadas que sí están en código.
- Hay otras que están parcialmente implementadas.
- Hay algunas inconsistencias reales entre documentación, rutas y código que conviene corregir antes de delegar mucho trabajo a un agente de IA.

---

## 2. Stack y base técnica

## Backend

- Framework: Laravel 13.
- PHP requerido: `^8.3`.
- Autorización por roles/permisos: `spatie/laravel-permission`.
- Sesiones: `database`.
- Cache: `database`.
- Queue: `database`.
- Base de datos configurada en `.env`: MySQL.

Archivo clave:

- [composer.json](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/composer.json)

Dependencias relevantes:

- `laravel/framework`
- `spatie/laravel-permission`
- `laravel/tinker`
- `phpunit/phpunit`
- `laravel/pint`

## Frontend

- Vite.
- Tailwind CSS 4.
- Alpine.js.
- Plugin `@alpinejs/focus`.

Archivos clave:

- [package.json](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/package.json)
- [resources/css/app.css](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/css/app.css)
- [resources/js/app.js](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/js/app.js)

## UI actual

La interfaz ya tiene un layout propio, no es el welcome por defecto de Laravel:

- Sidebar tipo rail expandible.
- Topbar.
- Command palette.
- Componentes Blade reutilizables.
- Estilo visual consistente con Tailwind.

Archivos base:

- [resources/views/components/layouts/app.blade.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/views/components/layouts/app.blade.php)
- [resources/views/components/layouts/partials/sidebar.blade.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/views/components/layouts/partials/sidebar.blade.php)
- [config/navigation.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/config/navigation.php)

---

## 3. Arquitectura del proyecto

El proyecto sigue una estructura bastante clara:

- `Controllers`: orquestan requests/responses.
- `FormRequests`: validación y autorización por caso.
- `DTOs`: normalizan datos de entrada.
- `Services`: concentran casos de uso.
- `Actions`: lógica de negocio puntual para creación/actualización/cierre.
- `Models`: relaciones, casts, scopes y helpers.
- `Enums`: estados, tipos y catálogos.
- `Views`: Blade para la parte web.

Esto significa que el proyecto ya está preparado para seguir creciendo de forma ordenada. Un agente de IA puede trabajar mejor aquí porque el código tiene separación razonable de responsabilidades.

---

## 4. Rutas implementadas

Se encontraron 82 rutas activas del proyecto.

## Rutas web principales

- Login/logout.
- Dashboard.
- CRUD de usuarios.
- CRUD de empleados.
- CRUD de unidades organizacionales.
- CRUD de activos.
- Listado/registro de movimientos.
- CRUD de campañas.
- CRUD de casos de mantenimiento.
- Gestión de ítems de mantenimiento.
- Gestión de documentos.
- Vista web de agentes.
- Configuración del sistema.
- CRUD de roles y permisos en prefijo `/admin`.

Archivo:

- [routes/web.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/routes/web.php)

## API implementada

Bajo prefijo `api/v1`:

- `POST /agents/register`
- `POST /agents/{uuid}/heartbeat`
- `POST /agents/{uuid}/sync`
- `POST /agents/{uuid}/snapshot`
- `POST /agents/{uuid}/bind-asset`
- `GET /organizational-units`
- `GET /employees/search`

Archivo:

- [routes/api.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/routes/api.php)

---

## 5. Middleware y seguridad

## Middleware registrados

Archivo:

- [bootstrap/app.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/bootstrap/app.php)

Aliases definidos:

- `user.active`
- `role`
- `permission`
- `role_or_permission`
- `agent.auth`

## Seguridad actual

- El sistema web usa `auth` + `user.active`.
- Los permisos se verifican tanto por middleware como por `authorize()` en varios `FormRequest`.
- Existe un `Gate::before()` que da acceso total al rol `admin`.

Archivos:

- [app/Http/Middleware/CheckUserIsActive.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Middleware/CheckUserIsActive.php)
- [app/Http/Middleware/AuthenticateAgent.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Middleware/AuthenticateAgent.php)
- [app/Providers/AppServiceProvider.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Providers/AppServiceProvider.php)

---

## 6. Módulos implementados

## 6.1 Autenticación

Estado: implementado.

Qué existe:

- Formulario de login.
- Login por `username` o `email`.
- Validación de contraseña.
- Bloqueo de acceso a usuarios inactivos.
- Logout con invalidación de sesión.

Archivos clave:

- [app/Http/Controllers/Auth/AuthController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/Auth/AuthController.php)
- [app/Services/Auth/AuthService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Auth/AuthService.php)
- [resources/views/auth/login.blade.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/views/auth/login.blade.php)

Observación:

- La documentación dice que se registra `last_login_at`, pero el `AuthService` actual no lo actualiza.

## 6.2 Dashboard

Estado: implementado.

Métricas cargadas:

- Total de activos.
- Activos en reparación.
- Casos abiertos.
- Campañas en curso.
- Técnicos activos.

También carga los 5 casos recientes no cerrados.

Archivo clave:

- [app/Http/Controllers/DashboardController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/DashboardController.php)

## 6.3 Usuarios

Estado: implementado.

Qué existe:

- Listado.
- Crear.
- Editar.
- Eliminar.
- Activar/desactivar.
- Asignación de rol.

Reglas implementadas:

- No puedes desactivarte a ti mismo.
- No puedes eliminarte a ti mismo.
- Debe existir al menos un admin.

Archivos clave:

- [app/Http/Controllers/UserController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/UserController.php)
- [app/Services/User/UserService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/User/UserService.php)
- [app/Actions/User/CreateUserAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/User/CreateUserAction.php)
- [app/Actions/User/UpdateUserAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/User/UpdateUserAction.php)

Views:

- `resources/views/users/*`

## 6.4 Roles y permisos

Estado: implementado.

Qué existe:

- CRUD de roles.
- CRUD de permisos.
- Roles semilla: `admin`, `tecnico`, `empleado`, `responsable_oficina`.
- Permisos granulares por módulo.
- Integración con Spatie.

Archivos clave:

- [app/Http/Controllers/Role/RoleController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/Role/RoleController.php)
- [app/Http/Controllers/Permission/PermissionController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/Permission/PermissionController.php)
- [app/Services/Role/RoleService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Role/RoleService.php)
- [app/Services/Permission/PermissionService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Permission/PermissionService.php)
- [database/seeders/RolePermissionSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/RolePermissionSeeder.php)

Observación importante:

- Las rutas están bajo `admin.roles.*` y `admin.permissions.*`, pero los controladores redirigen a `roles.index` y `permissions.index`. Eso parece un bug real de navegación/redirect.

## 6.5 Empleados

Estado: implementado.

Qué existe:

- CRUD completo.
- Filtros.
- Toggle de activo/inactivo.
- Relación con unidad organizacional.
- Relación con usuario.
- Soporte para técnicos.

Regla implementada:

- Si tiene activos asignados, no se elimina.

Archivos clave:

- [app/Http/Controllers/EmployeeController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/EmployeeController.php)
- [app/Services/Employee/EmployeeService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Employee/EmployeeService.php)
- [app/Models/Employee.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/Employee.php)

## 6.6 Unidades organizacionales

Estado: implementado casi completo.

Qué existe:

- CRUD completo.
- Jerarquía padre/hijo.
- Responsable de unidad.
- `full_path` calculado.
- Prevención de referencia circular.
- Restricciones para eliminar si hay hijos, empleados o activos.

Archivos clave:

- [app/Http/Controllers/OrganizationalUnitController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/OrganizationalUnitController.php)
- [app/Services/OrganizationalUnit/OrganizationalUnitService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/OrganizationalUnit/OrganizationalUnitService.php)
- [app/Models/OrganizationalUnit.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/OrganizationalUnit.php)

Observación importante:

- `StoreOrganizationalUnitRequest` autoriza con permiso `organizational-unit.create`, pero el sistema usa `org-unit.create`. Para usuarios no admin esto probablemente rompe el alta de unidades.

## 6.7 Activos

Estado: implementado.

Qué existe:

- CRUD completo.
- Soft delete.
- UUID generado automáticamente.
- Scopes de búsqueda/filtro.
- Relación con unidad, responsable, movimientos, casos, agente y documentos.

Regla implementada:

- No se elimina si tiene casos de mantenimiento abiertos.

Archivos clave:

- [app/Http/Controllers/AssetController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/AssetController.php)
- [app/Services/Asset/AssetService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Asset/AssetService.php)
- [app/Actions/Asset/CreateAssetAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/Asset/CreateAssetAction.php)
- [app/Actions/Asset/UpdateAssetAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/Asset/UpdateAssetAction.php)
- [app/Models/Asset.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/Asset.php)

## 6.8 Movimientos de activos

Estado: implementado.

Qué existe:

- Listado.
- Crear movimiento.
- Tipos por enum.
- Al crear movimiento, actualiza unidad/responsable del activo.
- Si hay empleado destino, el activo pasa a `en_uso`.

Archivos clave:

- [app/Http/Controllers/AssetMovementController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/AssetMovementController.php)
- [app/Services/AssetMovement/AssetMovementService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/AssetMovement/AssetMovementService.php)
- [app/Actions/AssetMovement/CreateMovementAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/AssetMovement/CreateMovementAction.php)
- [app/Models/AssetMovement.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/AssetMovement.php)

## 6.9 Campañas de mantenimiento

Estado: implementado.

Qué existe:

- CRUD completo.
- Código autogenerado (`CAMP-YYYY-NNNN`).
- Coordinador.
- Estado por enum.
- Relación con activos a través de `campaign_assets`.
- Agregar y remover activos de campaña.
- Técnico y fecha programada por activo.

Archivos clave:

- [app/Http/Controllers/MaintenanceCampaignController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/MaintenanceCampaignController.php)
- [app/Services/MaintenanceCampaign/MaintenanceCampaignService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/MaintenanceCampaign/MaintenanceCampaignService.php)
- [app/Actions/MaintenanceCampaign/CreateCampaignAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/MaintenanceCampaign/CreateCampaignAction.php)
- [app/Models/MaintenanceCampaign.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/MaintenanceCampaign.php)
- [app/Models/CampaignAsset.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/CampaignAsset.php)

Regla implementada:

- No se puede agregar el mismo activo dos veces a la misma campaña.

## 6.10 Casos de mantenimiento

Estado: implementado.

Qué existe:

- CRUD completo.
- Código autogenerado (`CASO-YYYYMM-NNNN`).
- Relación con activo.
- Relación opcional con campaña.
- Reportante.
- Técnico asignado.
- Tipo, prioridad, estado.
- Cierre de caso.
- Gestión de ítems.
- Documentos asociados.

Reglas implementadas:

- Casos `completado` o `cancelado` no se pueden editar.
- Al cerrar caso se calcula `total_cost` con los ítems si no se envía explícito.

Archivos clave:

- [app/Http/Controllers/MaintenanceCaseController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/MaintenanceCaseController.php)
- [app/Services/MaintenanceCase/MaintenanceCaseService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/MaintenanceCase/MaintenanceCaseService.php)
- [app/Actions/MaintenanceCase/CreateCaseAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/MaintenanceCase/CreateCaseAction.php)
- [app/Actions/MaintenanceCase/UpdateCaseAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/MaintenanceCase/UpdateCaseAction.php)
- [app/Actions/MaintenanceCase/CloseCaseAction.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Actions/MaintenanceCase/CloseCaseAction.php)
- [app/Models/MaintenanceCase.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/MaintenanceCase.php)
- [app/Models/MaintenanceItem.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/MaintenanceItem.php)

## 6.11 Documentos

Estado: implementado.

Qué existe:

- Listado.
- Carga de archivo.
- Descarga.
- Eliminación.
- Tipado por enum.
- Asociación por `reference_type` / `reference_id`.

Archivos clave:

- [app/Http/Controllers/DocumentController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/DocumentController.php)
- [app/Services/Document/DocumentService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Document/DocumentService.php)
- [app/Models/Document.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/Document.php)

Observación:

- El servicio elimina también el archivo físico si existe en disco.

## 6.12 Configuración

Estado: implementado.

Qué existe:

- Listado de settings no sensibles.
- Filtro por grupo.
- Edición de valor.
- Tipado `string`, `integer`, `boolean`, `json`, `text`.
- Helper `Setting::get()`.

Archivos clave:

- [app/Http/Controllers/SettingController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/SettingController.php)
- [app/Services/Setting/SettingService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/Setting/SettingService.php)
- [app/Models/Setting.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/Setting.php)

Observación:

- El modelo importa `Cache` pero no parece usar caché todavía.

## 6.13 Agentes de monitoreo

Estado: implementado en un nivel funcional real.

Qué existe:

- Registro de agente.
- Bearer token por dispositivo.
- Heartbeat.
- Snapshot.
- Sync.
- Vinculación administrativa a activo.
- Vista web de agentes.
- Historial de sincronizaciones.
- Actualización automática del activo con datos del agente.
- Creación automática de activos si llega binding/snapshot de un asset code no existente.

Archivos clave:

- [app/Http/Controllers/AgentDeviceController.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Http/Controllers/AgentDeviceController.php)
- [app/Services/AgentDevice/AgentDeviceService.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Services/AgentDevice/AgentDeviceService.php)
- [app/Models/AgentDevice.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/AgentDevice.php)
- [app/Models/AgentSync.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Models/AgentSync.php)

Lo que el servicio ya hace:

- Genera token plano y guarda hash SHA-256.
- Actualiza heartbeat y health.
- Persiste snapshots y syncs.
- Mezcla datos de snapshot/health hacia `specs_json` y `extra_json` del activo.
- Puede crear un activo tipo `computadora` automáticamente desde snapshot.

Observaciones importantes:

- La documentación habla de una `APP_AGENT_INSTALL_KEY`, pero no vi validación real de esa clave en `RegisterAgentRequest` ni en el controlador. El registro actual parece abierto.
- El estado `desconectado` existe en enum, pero no vi proceso automático que cambie un agente a ese estado; sólo existe el helper `isOnline()`.

## 6.14 AiLog

Estado: sólo estructura de base/modelo.

Qué existe:

- Migración.
- Modelo.
- Enum de estado.

No vi:

- Controlador.
- Servicio operativo.
- Rutas.
- UI.
- Casos de uso activos.

Conclusión:

- Es una base preparada, pero todavía no parece integrado funcionalmente.

---

## 7. Modelos y entidades disponibles

Modelos principales detectados:

- `User`
- `Role`
- `Permission`
- `Employee`
- `OrganizationalUnit`
- `Asset`
- `AssetMovement`
- `MaintenanceCampaign`
- `CampaignAsset`
- `MaintenanceCase`
- `MaintenanceItem`
- `Document`
- `Setting`
- `AgentDevice`
- `AgentSync`
- `AiLog`

Características generales:

- Uso amplio de enums en casts.
- Uso de arrays JSON (`specs_json`, `extra_json`, `scope_json`, `metrics_json`, `meta_json`, `payload_json`, etc.).
- Varios scopes para filtros.
- `Asset` usa soft delete.

---

## 8. Enums implementados

El sistema ya tiene catálogos cerrados por enum para evitar strings libres.

## Estados/tipos cubiertos

- Tipos de activo.
- Estados y condición de activos.
- Tipos de movimiento.
- Estados de campaña.
- Estados por activo dentro de campaña.
- Tipos, prioridades y estados de casos de mantenimiento.
- Tipos de ítems de mantenimiento.
- Tipos de documento.
- Estados de agente.
- Estados y tipos de sync.
- Tipos de setting.
- Roles.
- Permisos.
- Tipos de unidad organizacional.
- Estados de `AiLog`.

Carpeta:

- [app/Enums](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/app/Enums)

---

## 9. Base de datos y tablas del dominio

Migraciones del dominio detectadas:

- `users`
- tablas de Spatie permission
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
- cache/jobs/session auxiliares de Laravel

Carpeta:

- [database/migrations](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/migrations)

Observación:

- La conexión configurada apunta a una base MySQL que actualmente reporta 342 tablas en total. Eso sugiere que la BD puede estar compartida o contener más cosas aparte de este proyecto.

---

## 10. Datos semilla y datos actuales detectados

Pude verificar que la base configurada sí tiene datos.

Conteos actuales detectados:

- Usuarios: 6
- Empleados: 7
- Unidades organizacionales: 6
- Activos: 8
- Movimientos de activos: 3
- Campañas: 1
- Casos de mantenimiento: 3
- Ítems de mantenimiento: 3
- Documentos: 2
- Agentes: 2
- Syncs de agente: 84
- Settings: 4

## Seeders disponibles

- [database/seeders/DatabaseSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/DatabaseSeeder.php)
- [database/seeders/RolePermissionSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/RolePermissionSeeder.php)
- [database/seeders/SuperAdminSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/SuperAdminSeeder.php)
- [database/seeders/UserSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/UserSeeder.php)
- [database/seeders/OrganizationalUnitSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/OrganizationalUnitSeeder.php)
- [database/seeders/EmployeeSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/EmployeeSeeder.php)
- [database/seeders/AssetSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/AssetSeeder.php)
- [database/seeders/AssetMovementSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/AssetMovementSeeder.php)
- [database/seeders/MaintenanceSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/MaintenanceSeeder.php)
- [database/seeders/DocumentSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/DocumentSeeder.php)
- [database/seeders/SettingSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/SettingSeeder.php)
- [database/seeders/AgentSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/AgentSeeder.php)

## Datos demo/semilla relevantes

Usuarios demo sembrados:

- `superadmin@mdsj.local` / usuario `superadmin`
- `carlos.ramirez@mdsj.local` / usuario `cramirez`
- `lucia.quispe@mdsj.local` / usuario `lquispe`
- `diego.mendoza@mdsj.local` / usuario `dmendoza`
- `rosa.salazar@mdsj.local` / usuario `rsalazar`
- `miguel.vargas@mdsj.local` / usuario `mvargas`

La contraseña está definida directamente en los seeders, por lo que puede recuperarse revisando:

- [database/seeders/SuperAdminSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/SuperAdminSeeder.php)
- [database/seeders/UserSeeder.php](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/database/seeders/UserSeeder.php)

Datos demo funcionales existentes:

- 1 campaña en curso.
- 3 casos de mantenimiento con estados distintos.
- 3 ítems de mantenimiento.
- 2 documentos asociados.
- 2 agentes con historial.
- Activos repartidos entre OTI, Tesorería, Administración, Obras y Almacén.

Esto es suficiente para probar varios flujos sin sembrar nada adicional.

---

## 11. Vistas Blade disponibles

Módulos con vistas detectadas:

- `auth`
- `assets`
- `asset-movements`
- `campaigns`
- `maintenance-cases`
- `documents`
- `employees`
- `organizational-units`
- `users`
- `roles`
- `permissions`
- `settings`
- `agents`
- `dashboard`

Además existen componentes reutilizables:

- botones
- cards
- badges
- tablas
- dropdowns
- inputs/selects/textarea
- modal
- stat-card
- tabs
- flash messages
- icons

Carpeta:

- [resources/views](C:/Users/PracticanteOTI/Desktop/Mantenimiento y activos MDSJ/mantenimiento/resources/views)

---