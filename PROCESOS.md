# Sistema de Mantenimiento y Activos — MDSJ
## Documentación de Procesos del Sistema

---

## Índice

1. [Autenticación y control de acceso](#1-autenticación-y-control-de-acceso)
2. [Gestión de la estructura organizacional](#2-gestión-de-la-estructura-organizacional)
3. [Gestión de empleados](#3-gestión-de-empleados)
4. [Gestión de activos tecnológicos](#4-gestión-de-activos-tecnológicos)
5. [Movimientos de activos](#5-movimientos-de-activos)
6. [Campañas de mantenimiento](#6-campañas-de-mantenimiento)
7. [Casos de mantenimiento](#7-casos-de-mantenimiento)
8. [Ítems de mantenimiento](#8-ítems-de-mantenimiento)
9. [Gestión de documentos](#9-gestión-de-documentos)
10. [Agente de monitoreo (AgentSync)](#10-agente-de-monitoreo-agentsync)
11. [Registro de IA (AiLog)](#11-registro-de-ia-ailog)
12. [Configuración del sistema](#12-configuración-del-sistema)
13. [Administración de roles y permisos](#13-administración-de-roles-y-permisos)
14. [Dashboard](#14-dashboard)
15. [Modelo de permisos granulares](#15-modelo-de-permisos-granulares)

---

## 1. Autenticación y control de acceso

### 1.1 Inicio de sesión
- El usuario ingresa su **nombre de usuario** (o correo) y contraseña.
- El sistema valida que las credenciales sean correctas (`InvalidCredentialsException`).
- Si el usuario está **inactivo**, se rechaza el acceso (`UserInactiveException`).
- En caso exitoso, se crea la sesión y se registra `last_login_at`.
- Soporte para sesión "recordar" (remember me).

### 1.2 Cierre de sesión
- Invalida la sesión activa del usuario autenticado.

### 1.3 Middleware `user.active`
- Verificación en cada request: si el usuario fue desactivado mientras tenía sesión activa, se cierra automáticamente.

### 1.4 Gestión de usuarios del sistema
| Acción | Permiso requerido |
|---|---|
| Ver listado | `user.view` |
| Crear usuario | `user.create` |
| Editar usuario | `user.edit` |
| Activar/desactivar | `user.edit` |
| Eliminar usuario | `user.delete` |

**Reglas de negocio:**
- Un usuario no puede desactivarse/eliminarse a sí mismo.
- Debe existir al menos un usuario con rol `admin` en todo momento.
- Al crear un usuario se le puede vincular con un empleado existente.
- Al crear/actualizar se asigna exactamente un rol (gestionado con Spatie).

---

## 2. Gestión de la estructura organizacional

Gestión de la jerarquía institucional de la MDSJ (gerencias, subgerencias, oficinas, sedes).

### 2.1 Tipos de unidad
`GERENCIA` → `OFICINA_GENERAL` → `SUBGERENCIA` → `OFICINA` → `SEDE`

### 2.2 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver listado / detalle | `org-unit.view` |
| Crear unidad | `org-unit.create` |
| Editar unidad | `org-unit.edit` |
| Eliminar unidad | `org-unit.delete` |

**Reglas de negocio:**
- Una unidad **no puede eliminarse** si tiene unidades hijas, empleados asignados o activos registrados.
- No se permite crear referencias circulares en la jerarquía padre-hijo.
- Se puede asignar un empleado como **responsable** de la unidad.
- Atributo calculado `full_path`: devuelve la ruta jerárquica completa (ej. `Gerencia > Subgerencia > Oficina`).

---

## 3. Gestión de empleados

Registro y administración del personal de la institución.

### 3.1 Datos gestionados
- DNI, nombre completo, correo, teléfono, cargo, unidad organizacional.
- Indicador de **técnico**: si es técnico, puede ser asignado a casos de mantenimiento.
- Especialidad y nivel técnico (para técnicos).
- Estado activo/inactivo.

### 3.2 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver listado / detalle | `employee.view` |
| Registrar empleado | `employee.create` |
| Editar empleado | `employee.edit` |
| Activar/desactivar | `employee.edit` |
| Eliminar empleado | `employee.delete` |

**Reglas de negocio:**
- Un empleado con activos asignados no puede eliminarse directamente (deben reasignarse primero).
- Un empleado puede tener asociado un único usuario del sistema.
- El filtro de técnicos solo muestra empleados con `is_technician = true` y `is_active = true`.

---

## 4. Gestión de activos tecnológicos

Inventario centralizado de todos los equipos e infraestructura TI de la institución.

### 4.1 Datos del activo
- Código interno, código patrimonial, nombre, tipo, marca, modelo, número de serie.
- Estado operativo: `activo`, `en_uso`, `en_almacén`, `en_reparación`, `dado_de_baja`, `extraviado`.
- Condición física: `bueno`, `regular`, `malo`, `obsoleto`.
- Unidad organizacional y empleado responsable.
- Fecha de compra, valor de referencia.
- Especificaciones técnicas y datos adicionales (campos JSON flexibles).
- UUID único para identificación externa (usado por el agente).
- Soft delete: los activos dados de baja no se eliminan físicamente.

### 4.2 Tipos de activo soportados
Computadora, Laptop, Servidor, Impresora, Escáner, Monitor, Tablet, Teléfono IP, Proyector, UPS, Switch, Router, Access Point, Cámara, Disco externo, Otro.

### 4.3 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver inventario / detalle | `asset.view` |
| Registrar activo | `asset.create` |
| Editar activo | `asset.edit` |
| Eliminar (baja) | `asset.delete` |

**Reglas de negocio:**
- Un activo con casos de mantenimiento activos (no cerrados) no puede eliminarse.
- El UUID se genera automáticamente al crear el activo.
- Desde el detalle del activo se puede ver el historial de movimientos, casos de mantenimiento, documentos y el agente de monitoreo vinculado.

---

## 5. Movimientos de activos

Trazabilidad completa de todos los desplazamientos físicos y cambios de responsabilidad de los activos.

### 5.1 Tipos de movimiento
| Tipo | Descripción |
|---|---|
| `asignacion` | Asignación inicial a una persona u oficina |
| `traslado` | Cambio de unidad organizacional |
| `devolucion` | Retorno al almacén o área central |
| `baja` | Retirada definitiva del inventario activo |
| `ingreso` | Alta de un nuevo activo al sistema |
| `prestamo` | Préstamo temporal a otra oficina o persona |

### 5.2 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver historial | `asset-movement.view` |
| Registrar movimiento | `asset-movement.create` |

**Reglas de negocio:**
- Al registrar un movimiento, el sistema actualiza automáticamente la `organizational_unit_id` y el `responsible_employee_id` del activo según destino.
- Si el movimiento tiene empleado destino, el estado del activo pasa a `en_uso`.
- Cada movimiento registra quién lo creó (`created_by`).
- Se puede adjuntar un número de documento de respaldo (acta, resolución, etc.).

---

## 6. Campañas de mantenimiento

Planificación y ejecución de mantenimientos masivos programados sobre un conjunto de activos.

### 6.1 Ciclo de vida de una campaña
```
PLANIFICADA → EN_CURSO → COMPLETADA
                       ↘ CANCELADA
     ↕ PAUSADA (desde EN_CURSO)
```

### 6.2 Datos de la campaña
- Código autogenerado (`CAMP-YYYY-NNNN`), nombre, objetivo, fechas de inicio y fin.
- Coordinador (empleado responsable de la campaña).
- Alcance (JSON flexible: puede incluir filtros por tipo, unidad, etc.).
- Resumen y métricas al cierre.

### 6.3 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver campañas / detalle | `campaign.view` |
| Crear campaña | `campaign.create` |
| Editar campaña | `campaign.edit` |
| Agregar activos a campaña | `campaign.edit` |
| Remover activos de campaña | `campaign.edit` |
| Eliminar campaña | `campaign.delete` |

### 6.4 Gestión de activos en campaña (tabla `campaign_assets`)
Cada activo dentro de una campaña tiene su propio estado de atención:

| Estado | Descripción |
|---|---|
| `pendiente` | Programado, aún no atendido |
| `programado` | Tiene fecha y técnico asignado |
| `atendido` | Se generó un caso de mantenimiento y se completó |
| `omitido` | No fue posible atenderlo en la campaña |

**Reglas de negocio:**
- Un activo no puede agregarse dos veces a la misma campaña.
- Se puede asignar un técnico y fecha por cada activo dentro de la campaña.
- Al atender un activo, se vincula el `maintenance_case_id` correspondiente.

---

## 7. Casos de mantenimiento

Registro detallado de cada intervención técnica realizada sobre un activo, ya sea originada desde una campaña o de forma individual (correctivo/emergencia).

### 7.1 Ciclo de vida de un caso
```
PENDIENTE → EN_PROGRESO → COMPLETADO
          ↘ EN_ESPERA  ↗
          ↘ CANCELADO
```

### 7.2 Datos del caso
- Código autogenerado (`CASO-YYYYMM-NNNN`).
- Activo intervenido, campaña (opcional), tipo de mantenimiento, prioridad.
- Empleado que reporta el problema y técnico asignado.
- Descripción del problema, diagnóstico, acciones realizadas.
- Fechas de inicio y cierre.
- Próxima fecha de mantenimiento sugerida.
- Datos de conformidad: nombre del responsable que firma y fecha.
- Costo total calculado a partir de los ítems.

### 7.3 Tipos de mantenimiento
| Tipo | Cuándo se usa |
|---|---|
| `preventivo` | Mantenimiento planificado, activo funcional |
| `correctivo` | Reparación de falla existente |
| `predictivo` | Basado en análisis de datos/tendencias |
| `emergencia` | Falla crítica que requiere atención inmediata |

### 7.4 Prioridades
`baja` → `media` → `alta` → `critica`

### 7.5 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver casos / detalle | `maintenance-case.view` |
| Crear caso | `maintenance-case.create` |
| Editar caso | `maintenance-case.edit` |
| Cerrar caso | `maintenance-case.close` |
| Eliminar caso | `maintenance-case.delete` |

**Proceso de cierre de caso:**
1. El técnico completa las acciones realizadas.
2. Registra el nombre y fecha de conformidad del responsable del área.
3. El sistema calcula el costo total sumando todos los ítems.
4. El estado cambia a `COMPLETADO` y se registra `finished_at`.
5. Se puede registrar la próxima fecha de mantenimiento recomendada.

**Reglas de negocio:**
- Un caso `COMPLETADO` o `CANCELADO` no puede modificarse.
- El caso puede generarse independientemente o estar vinculado a una campaña.

---

## 8. Ítems de mantenimiento

Registro de materiales, repuestos y servicios utilizados en cada caso de mantenimiento. Permite calcular el costo real de la intervención.

### 8.1 Tipos de ítem
| Tipo | Descripción |
|---|---|
| `repuesto` | Pieza o componente reemplazado |
| `insumo` | Material consumible (pasta térmica, limpiadores, etc.) |
| `servicio` | Servicio externo contratado |
| `herramienta` | Herramienta especial utilizada |

### 8.2 Procesos
- Agregar ítem a un caso → `maintenance-case.edit`
- Eliminar ítem de un caso → `maintenance-case.edit`
- El `total_cost` se calcula automáticamente: `quantity × unit_cost`.
- Al cerrar el caso, el `total_cost` del caso se calcula sumando todos sus ítems.

---

## 9. Gestión de documentos

Repositorio de archivos digitales asociados a activos, casos de mantenimiento u otras entidades del sistema.

### 9.1 Tipos de documento
| Tipo | Descripción |
|---|---|
| `acta_entrega` | Acta de asignación o entrega de activo |
| `orden_mantenimiento` | Orden de trabajo técnico |
| `informe_tecnico` | Informe detallado post-mantenimiento |
| `inventario` | Reporte de inventario exportado |
| `otro` | Cualquier otro documento de soporte |

### 9.2 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver documentos | `document.view` |
| Subir documento | `document.create` |
| Descargar documento | `document.view` |
| Eliminar documento | `document.delete` |

**Reglas de negocio:**
- Los documentos se almacenan físicamente en `storage/app/documents/YYYY/MM/`.
- Al eliminar un documento, se borra también el archivo físico del disco.
- Cada documento está asociado mediante polimorfismo a su entidad de origen (`reference_type` / `reference_id`).
- Formatos aceptados: PDF, DOC, DOCX, XLSX, JPG, PNG (máx. 10 MB).

---

## 10. Agente de monitoreo (AgentSync)

Sistema de agentes de software instalados en los equipos que permiten la sincronización automática del estado del hardware con el inventario del sistema.

### 10.1 Arquitectura
```
[Activo físico]
      │
  [Agente instalado]
      │  HTTP/API (Bearer token)
      ▼
[API del sistema — routes/api.php]
      │
  [AgentDeviceService]
      │
  [Base de datos]
```

### 10.2 Ciclo de vida del agente

#### Registro (una sola vez)
1. El agente envía `POST /api/v1/agents/register` con los datos del equipo y el código interno del activo.
2. El sistema valida que el activo existe y no tiene un agente ya registrado.
3. Se crea el registro `agent_devices` con UUID y `api_token` único (SHA-256).
4. El sistema devuelve el UUID y el token al agente (única vez que el token es visible en claro).

#### Heartbeat (periódico)
- El agente envía `POST /api/v1/agents/{uuid}/heartbeat` cada N minutos.
- El sistema actualiza `last_heartbeat_at`, IP y versión del agente.
- Se registra un sync de tipo `heartbeat` en `agent_syncs`.
- Un agente se considera **desconectado** si no envía heartbeat en más de 10 minutos.

#### Sincronización de datos
- **Snapshot completo** (`sync_type: snapshot`): envía el estado completo del hardware (CPU, RAM, disco, OS, etc.) en `payload_json`.
- **Delta de cambios** (`sync_type: delta`): envía solo los cambios detectados desde el último snapshot en `detected_changes_json`.
- El sistema actualiza `last_snapshot_json` en el dispositivo y registra el sync.

### 10.3 Estados del dispositivo
| Estado | Descripción |
|---|---|
| `activo` | Heartbeat reciente (≤ 10 min) |
| `inactivo` | Desactivado manualmente |
| `desconectado` | Sin heartbeat reciente (> 10 min) |

### 10.4 Procesos web (admin)
| Acción | Permiso requerido |
|---|---|
| Ver listado de agentes | `agent.view` |
| Ver detalle / historial de syncs | `agent.view` |
| Gestionar agentes | `agent.manage` |

---

## 11. Registro de IA (AiLog)

Módulo de trazabilidad de interacciones con modelos de inteligencia artificial, para auditoría y mejora continua de las sugerencias automatizadas del sistema.

### 11.1 Datos registrados
- Usuario que realizó la consulta.
- Entidad de contexto (activo, caso, campaña, etc.) mediante polimorfismo.
- Mensaje enviado y respuesta recibida.
- Estado del procesamiento: `pendiente`, `exitoso`, `error`.
- Acción aplicada a partir de la sugerencia de la IA.
- Metadatos adicionales (tokens usados, modelo, tiempo de respuesta, etc.).

---

## 12. Configuración del sistema

Parámetros operativos del sistema ajustables por administradores sin necesidad de despliegue de código.

### 12.1 Tipos de configuración soportados
`string`, `integer`, `boolean`, `json`, `text`

### 12.2 Organización
Los parámetros se agrupan por `group_name` (ej. `mantenimiento`, `notificaciones`, `agente`, `sistema`).

### 12.3 Procesos
| Acción | Permiso requerido |
|---|---|
| Ver configuraciones | `setting.view` |
| Editar valor | `setting.edit` |

**Reglas de negocio:**
- Los parámetros marcados como `is_sensitive = true` no se listan en la interfaz.
- El valor se tipifica automáticamente al leerlo (`Setting::get('clave')`).

---

## 13. Administración de roles y permisos

Gestión del control de acceso basado en roles granulares (Spatie Laravel Permission).

### 13.1 Roles predefinidos
| Rol | Descripción |
|---|---|
| `admin` | Administrador OTI — acceso completo |
| `tecnico` | Técnico de mantenimiento |
| `empleado` | Empleado general de la institución |
| `responsable_oficina` | Responsable de una unidad organizacional |

### 13.2 Procesos (solo rol `admin`)
- Crear, editar y eliminar **roles**.
- Asignar/desasignar **permisos** a roles.
- Crear, editar y eliminar **permisos** individuales.

### 13.3 Asignación de permisos a usuarios
- Cada usuario tiene exactamente **un rol** asignado.
- Los permisos efectivos del usuario son los del rol al que pertenece.
- Los permisos se verifican en cada acción mediante `$this->authorize('permiso.accion')` o `can('permiso.accion')` en los Form Requests.

---

## 14. Dashboard

Panel de control con métricas operativas en tiempo real.

### 14.1 Indicadores mostrados
| Indicador | Descripción |
|---|---|
| Total de activos | Cantidad de activos registrados |
| Activos en reparación | Activos con estado `en_reparacion` |
| Casos abiertos | Casos no cerrados ni cancelados |
| Campañas en curso | Campañas con estado `en_curso` |
| Técnicos activos | Empleados técnicos con `is_active = true` |

### 14.2 Vista de casos recientes
Lista de los 5 casos de mantenimiento más recientes que aún están abiertos, con enlace directo a su detalle.

---

## 15. Modelo de permisos granulares

Tabla completa de permisos del sistema y las acciones que protegen.

| Permiso | Módulo | Descripción |
|---|---|---|
| `user.view` | Usuarios | Ver listado y detalle |
| `user.create` | Usuarios | Crear nuevos usuarios |
| `user.edit` | Usuarios | Editar / activar-desactivar |
| `user.delete` | Usuarios | Eliminar usuarios |
| `employee.view` | Empleados | Ver listado y detalle |
| `employee.create` | Empleados | Registrar empleados |
| `employee.edit` | Empleados | Editar / activar-desactivar |
| `employee.delete` | Empleados | Eliminar empleados |
| `org-unit.view` | Unidades organizacionales | Ver listado y detalle |
| `org-unit.create` | Unidades organizacionales | Crear unidades |
| `org-unit.edit` | Unidades organizacionales | Editar unidades |
| `org-unit.delete` | Unidades organizacionales | Eliminar unidades |
| `asset.view` | Activos | Ver inventario y detalle |
| `asset.create` | Activos | Registrar activos |
| `asset.edit` | Activos | Editar activos |
| `asset.delete` | Activos | Dar de baja activos |
| `asset-movement.view` | Movimientos | Ver historial de movimientos |
| `asset-movement.create` | Movimientos | Registrar movimientos |
| `campaign.view` | Campañas | Ver campañas |
| `campaign.create` | Campañas | Crear campañas |
| `campaign.edit` | Campañas | Editar / gestionar activos en campaña |
| `campaign.delete` | Campañas | Eliminar campañas |
| `maintenance-case.view` | Casos | Ver casos |
| `maintenance-case.create` | Casos | Crear casos |
| `maintenance-case.edit` | Casos | Editar casos / gestionar ítems |
| `maintenance-case.close` | Casos | Cerrar casos completados |
| `maintenance-case.delete` | Casos | Eliminar casos |
| `document.view` | Documentos | Ver y descargar documentos |
| `document.create` | Documentos | Subir documentos |
| `document.delete` | Documentos | Eliminar documentos |
| `setting.view` | Configuración | Ver parámetros del sistema |
| `setting.edit` | Configuración | Modificar valores de configuración |
| `agent.view` | Agentes | Ver dispositivos agente |
| `agent.manage` | Agentes | Gestionar agentes (activar/desactivar) |

---

## Flujos transversales

### Flujo completo: Mantenimiento correctivo individual
```
1. Se detecta falla en un activo
2. Se crea un Caso de mantenimiento (tipo: correctivo)
3. Se asigna un técnico
4. El técnico actualiza el diagnóstico y acciones
5. Se agregan los ítems utilizados (repuestos, insumos)
6. El técnico cierra el caso registrando la conformidad
7. Se genera un documento (informe técnico)
8. El sistema actualiza el costo total del caso
```

### Flujo completo: Campaña de mantenimiento preventivo
```
1. Se planifica la campaña (fechas, coordinador, objetivo)
2. Se agregan los activos a atender (con técnico y fecha por activo)
3. La campaña pasa a EN_CURSO
4. Por cada activo:
   a. El técnico crea un Caso de mantenimiento vinculado a la campaña
   b. Registra acciones e ítems
   c. Cierra el caso → el activo en campaña pasa a ATENDIDO
5. Al concluir todos los activos, la campaña pasa a COMPLETADA
6. Se registra el resumen y métricas de la campaña
```

### Flujo completo: Incorporación de activo con agente
```
1. Se registra el activo en el sistema (AssetController)
2. Se instala el agente en el equipo físico
3. El agente ejecuta el registro inicial (POST /api/v1/agents/register)
4. El sistema vincula el agente al activo y genera el token
5. El agente comienza a enviar heartbeats periódicos
6. Periódicamente envía snapshots del hardware
7. OTI monitorea el estado desde /agents en el panel web
```
