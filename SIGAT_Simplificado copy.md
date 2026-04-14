# Sistema Integral de Gestión de Activos Tecnológicos (SIGAT) — Versión Simplificada

## 1. Resumen ejecutivo

Se propone desarrollar una versión simplificada del **Sistema Integral de Gestión de Activos Tecnológicos (SIGAT)** para una municipalidad, priorizando rapidez de desarrollo, facilidad de mantenimiento, menor cantidad de tablas y una arquitectura funcional pero realista.

Esta versión conserva los procesos esenciales del sistema original:

- inventario de activos tecnológicos,
- asignación y movimientos,
- mantenimiento individual,
- campañas de mantenimiento,
- generación de documentos,
- reportes básicos,
- auditoría y trazabilidad,
- configuración general,
- integración opcional con agente local,
- integración opcional con IA.

La diferencia principal es que el sistema ya no se divide en muchos módulos especializados ni en decenas de tablas auxiliares. En su lugar, se agrupa en **5 módulos principales**, con una estructura de datos más compacta y orientada a salir rápido a producción.

---

## 2. Objetivo general

Diseñar e implementar un sistema centralizado que permita registrar, controlar y administrar los activos tecnológicos de una municipalidad durante su ciclo de vida, así como sus mantenimientos, asignaciones, campañas, documentos y trazabilidad, en una solución simple, escalable y funcional.

---

## 3. Objetivos específicos

- Registrar todos los activos tecnológicos de la municipalidad con sus datos principales.
- Conocer dónde se encuentra cada activo y quién es su responsable actual.
- Registrar mantenimientos preventivos y correctivos.
- Gestionar campañas de mantenimiento masivo.
- Generar documentos operativos básicos.
- Obtener reportes útiles para control técnico y administrativo.
- Mantener trazabilidad de las operaciones más importantes.
- Permitir integración opcional con un agente local para capturar información técnica real desde equipos.
- Permitir integración opcional con IA como apoyo asistido, sin volverla obligatoria.

---

## 4. Principios de simplificación

Esta versión del sistema se basa en los siguientes principios:

### 4.1. Menos módulos

El sistema ya no se organiza en 10 o 12 módulos separados. Se concentra en **5 módulos principales**, agrupando funciones relacionadas.

### 4.2. Menos tablas

Se eliminan tablas excesivamente fragmentadas y se fusionan entidades que pueden convivir en una sola estructura sin afectar la operación.

### 4.3. Catálogos mínimos

Los catálogos se reducen a lo estrictamente necesario. Los valores secundarios pueden manejarse desde configuración o campos controlados por código.

### 4.4. Uso de JSON solo donde conviene

Los datos muy variables, técnicos o poco consultados se guardan en campos JSON para evitar crear muchas tablas auxiliares.

### 4.5. Primero lo operativo

El sistema debe servir primero para operar: registrar, consultar, mover, mantener, documentar y reportar. Los motores avanzados, automatizaciones complejas y configuradores visuales se dejan para una fase posterior.

---

## 5. Alcance funcional

El sistema abarcará los siguientes procesos:

1. Administración de usuarios y acceso.
2. Registro de estructura organizacional.
3. Registro de personal responsable y técnico.
4. Inventario de activos tecnológicos.
5. Registro de asignaciones y movimientos.
6. Gestión de mantenimientos individuales.
7. Gestión de campañas de mantenimiento.
8. Generación de documentos operativos.
9. Reportes básicos de control.
10. Auditoría y trazabilidad.
11. Configuración general.
12. Integración opcional con agente local.
13. Integración opcional con IA.

---

## 6. Tipos de activos considerados

Como mínimo, el sistema debe permitir registrar:

- computadoras de escritorio,
- laptops,
- impresoras,
- escáneres,
- UPS,
- monitores,
- servidores,
- switches,
- routers,
- puntos de acceso,
- cámaras,
- proyectores,
- tablets,
- equipos biométricos,
- periféricos importantes,
- componentes relevantes,
- activos tecnológicos que la municipalidad considere necesarios.

---

## 7. Estructura simplificada del sistema

La nueva estructura funcional queda así:

```text
SIGAT
├── 1. Seguridad
├── 2. Organización
├── 3. Activos
├── 4. Mantenimiento
└── 5. Soporte
```

### 7.1. Explicación de la simplificación

- **Seguridad** concentra usuarios, roles y acceso.
- **Organización** concentra la estructura institucional y el personal.
- **Activos** concentra inventario, adjuntos, asignaciones y movimientos.
- **Mantenimiento** concentra mantenimientos individuales y campañas.
- **Soporte** concentra documentos, auditoría, configuración, notificaciones y las integraciones opcionales con agente local e IA.

Con esto se reduce notablemente la complejidad conceptual del sistema.

---

## 8. Módulos principales del sistema

## 8.1. Módulo de Seguridad

Este módulo controla el acceso al sistema.

### Funcionalidades

- Inicio de sesión.
- Cierre de sesión.
- Gestión de usuarios.
- Gestión de roles.
- Asignación de roles a usuarios.
- Restricción de acceso según permisos del rol.

### Roles sugeridos

- Administrador del sistema
- Administrador TI
- Técnico de soporte
- Supervisor
- Responsable de oficina
- Usuario consulta

---

## 8.2. Módulo de Organización

Este módulo registra la estructura municipal y el personal relacionado con los activos.

### Funcionalidades

- Registrar unidades organizacionales.
- Mantener jerarquía institucional.
- Registrar empleados o responsables.
- Registrar técnicos de soporte.
- Registrar proveedores.

### Simplificación aplicada

En lugar de manejar tablas separadas para sede, gerencia, subgerencia, oficina, área y ubicación, se usará una sola estructura jerárquica llamada `organizational_units`.

### Tipos posibles de unidad

- sede
- gerencia
- subgerencia
- oficina
- área
- ubicación

### Beneficio

Esto permite representar toda la estructura municipal con menos tablas y más flexibilidad.

---

## 8.3. Módulo de Activos

Este módulo concentra el inventario principal del sistema.

### Funcionalidades

- Registrar activos tecnológicos.
- Editar información del activo.
- Asociar responsable actual.
- Asociar ubicación actual.
- Consultar estado del activo.
- Adjuntar archivos.
- Registrar movimientos y cambios de responsable.
- Consultar historial básico del activo.

### Información principal del activo

Cada activo debe poder registrar como mínimo:

- código interno,
- código patrimonial,
- nombre,
- tipo,
- marca,
- modelo,
- número de serie,
- estado,
- condición,
- unidad organizacional actual,
- empleado responsable,
- proveedor,
- fecha de compra,
- valor referencial,
- observaciones.

### Simplificación aplicada

No se crearán múltiples tablas para atributos, componentes, licencias, tags e historial de estado en la primera versión. La información variable se manejará en campos JSON dentro del propio activo o como adjuntos.

### Datos variables sugeridos para JSON

- especificaciones técnicas,
- componentes relevantes,
- licencias asociadas,
- datos de garantía,
- información adicional del equipo.

---

## 8.4. Módulo de Mantenimiento

Este módulo concentra tanto los mantenimientos individuales como las campañas de mantenimiento.

### Funcionalidades

- Registrar incidencias o solicitudes.
- Crear casos de mantenimiento.
- Asignar técnico responsable.
- Registrar diagnóstico.
- Registrar acciones realizadas.
- Registrar tareas, repuestos o costos.
- Cambiar estado del caso.
- Programar próximo mantenimiento.
- Gestionar campañas de mantenimiento masivo.
- Relacionar activos atendidos dentro de una campaña.

### Simplificación aplicada

En vez de usar muchas tablas separadas para solicitudes, órdenes, registros, tareas, partes, costos, firmas y programaciones, se usará una estructura principal llamada `maintenance_cases` y una tabla de detalle llamada `maintenance_items`.

### Tipos de mantenimiento

- preventivo
- correctivo
- diagnóstico
- emergencia

### Estados sugeridos del caso

- registrado
- asignado
- en_proceso
- atendido
- observado
- cerrado
- cancelado

### Campañas de mantenimiento

Las campañas seguirán existiendo porque sí son una entidad importante del negocio, pero con una estructura más simple.

Cada campaña debe permitir:

- definir nombre y objetivo,
- definir alcance,
- establecer fechas,
- asignar técnicos,
- cargar activos objetivo,
- controlar avance,
- cerrar con resumen ejecutivo.

---

## 8.5. Módulo de Soporte

Este módulo reúne todo lo transversal al sistema.

### Subfunciones incluidas

- documentos,
- auditoría,
- configuración,
- notificaciones,
- integración opcional con agente local,
- integración opcional con IA.

### ¿Por qué se agrupan?

Porque todas estas funciones apoyan al negocio, pero no constituyen el núcleo operativo principal. Mantenerlas juntas reduce dispersión y simplifica la arquitectura inicial.

### 8.5.1. Documentos

El sistema debe generar al menos:

- ficha técnica del activo,
- acta de mantenimiento,
- orden de trabajo,
- constancia de entrega o devolución,
- resumen de campaña.

#### Simplificación aplicada

No se implementará desde la primera versión un motor complejo de plantillas en base de datos. Las plantillas podrán manejarse en código, por ejemplo con Blade + PDF.

### 8.5.2. Auditoría

Se registrarán acciones relevantes del sistema:

- creación,
- edición,
- eliminación lógica,
- cambios de estado,
- movimientos,
- generación de documentos,
- cierres de mantenimiento,
- acciones sobre campañas.

### 8.5.3. Configuración

Se mantendrán parámetros generales del sistema, por ejemplo:

- nombre de la institución,
- datos del encabezado documental,
- valores por defecto,
- configuración básica de integraciones,
- opciones habilitadas.

### 8.5.4. Notificaciones

Las notificaciones pueden comenzar de manera simple:

- avisos dentro del sistema,
- avisos por correo si se requiere,
- recordatorios de campañas o mantenimientos.

### 8.5.5. Integración opcional con agente local

El agente local será una integración opcional que permitirá leer información real desde PCs institucionales.

#### Funcionalidades mínimas del agente

- registrar equipo,
- enviar heartbeat,
- enviar snapshot técnico,
- vincularse a un activo,
- reportar cambios principales.

#### Simplificación aplicada

No se dividirá el agente en muchas tablas de hardware, software y red. Se manejará una tabla principal del dispositivo y una tabla de sincronizaciones con payload JSON.

### 8.5.6. Integración opcional con IA

La IA será solo un apoyo opcional.

#### Usos posibles

- redactar observaciones técnicas,
- resumir historial,
- sugerir diagnósticos,
- apoyar en informes,
- responder consultas naturales.

#### Regla de diseño

La IA no debe bloquear la operación del sistema. Si falla, el sistema sigue funcionando normalmente.

---

## 9. Modelo de datos simplificado

La propuesta base se reduce a **19 tablas principales**.

## 9.1. Seguridad

### `users`
Usuarios del sistema.

Campos sugeridos:

- id
- name
- email
- password
- role_id
- employee_id
- is_active
- last_login_at
- created_at
- updated_at

### `roles`
Roles del sistema.

Campos sugeridos:

- id
- name
- slug
- description
- created_at
- updated_at

---

## 9.2. Organización

### `organizational_units`
Estructura jerárquica de la municipalidad.

Campos sugeridos:

- id
- parent_id
- type
- code
- name
- responsible_employee_id
- meta_json
- is_active
- created_at
- updated_at

### `employees`
Personal interno y técnicos.

Campos sugeridos:

- id
- dni
- full_name
- email
- phone
- position
- organizational_unit_id
- is_technician
- specialty
- level
- is_active
- created_at
- updated_at

### `suppliers`
Proveedores relacionados con activos o mantenimiento.

Campos sugeridos:

- id
- ruc
- business_name
- contact_name
- phone
- email
- address
- notes
- created_at
- updated_at

---

## 9.3. Activos

### `assets`
Inventario principal.

Campos sugeridos:

- id
- uuid
- internal_code
- patrimonial_code
- name
- asset_type
- brand
- model
- serial_number
- status
- condition
- organizational_unit_id
- responsible_employee_id
- supplier_id
- purchase_date
- reference_value
- specs_json
- extra_json
- notes
- created_at
- updated_at

### `attachments`
Archivos adjuntos del sistema.

Campos sugeridos:

- id
- attachable_type
- attachable_id
- file_type
- file_name
- file_path
- mime_type
- size
- uploaded_by
- created_at

### `asset_movements`
Movimientos, asignaciones y devoluciones.

Campos sugeridos:

- id
- asset_id
- movement_type
- origin_unit_id
- destination_unit_id
- from_employee_id
- to_employee_id
- movement_date
- reason
- document_number
- notes
- created_by
- created_at
- updated_at

---

## 9.4. Mantenimiento

### `maintenance_cases`
Caso principal de mantenimiento.

Campos sugeridos:

- id
- code
- asset_id
- campaign_id
- reported_by_employee_id
- assigned_technician_id
- maintenance_type
- priority
- status
- problem_description
- diagnosis
- actions_taken
- started_at
- finished_at
- next_maintenance_date
- conformity_name
- conformity_date
- total_cost
- notes
- created_by
- created_at
- updated_at

### `maintenance_items`
Detalle del caso de mantenimiento.

Campos sugeridos:

- id
- maintenance_case_id
- item_type
- name
- description
- quantity
- unit_cost
- total_cost
- data_json
- created_at
- updated_at

### `maintenance_campaigns`
Campañas de mantenimiento.

Campos sugeridos:

- id
- code
- name
- objective
- scope_json
- start_date
- end_date
- status
- coordinator_employee_id
- summary
- metrics_json
- created_by
- created_at
- updated_at

### `campaign_assets`
Activos incluidos en campañas.

Campos sugeridos:

- id
- campaign_id
- asset_id
- assigned_technician_id
- scheduled_date
- attended_date
- status
- maintenance_case_id
- notes
- created_at
- updated_at

---

## 9.5. Soporte

### `documents`
Documentos generados por el sistema.

Campos sugeridos:

- id
- document_type
- reference_type
- reference_id
- code
- title
- file_path
- generated_by
- generated_at
- meta_json
- created_at
- updated_at

### `activity_logs`
Auditoría y trazabilidad.

Campos sugeridos:

- id
- user_id
- action
- entity_type
- entity_id
- before_json
- after_json
- ip_address
- route
- user_agent
- created_at

### `settings`
Configuración general.

Campos sugeridos:

- id
- key
- value
- type
- group_name
- description
- is_sensitive
- created_at
- updated_at

### `notifications`
Notificaciones internas.

Campos sugeridos:

- id
- user_id
- type
- title
- message
- is_read
- read_at
- meta_json
- created_at
- updated_at

### `agent_devices`
Equipos detectados por el agente local.

Campos sugeridos:

- id
- uuid
- asset_id
- hostname
- serial_number
- device_model
- operating_system
- agent_version
- last_ip
- last_heartbeat_at
- status
- last_snapshot_json
- created_at
- updated_at

### `agent_syncs`
Sincronizaciones del agente.

Campos sugeridos:

- id
- agent_device_id
- sync_type
- payload_json
- detected_changes_json
- status
- synced_at
- created_at

### `ai_logs`
Registro de interacciones con IA.

Campos sugeridos:

- id
- user_id
- context_type
- context_id
- prompt
- response
- status
- applied_action
- meta_json
- created_at
- updated_at

---

## 10. Relaciones principales

- Un rol tiene muchos usuarios.
- Una unidad organizacional puede tener una unidad padre.
- Una unidad organizacional puede tener muchos empleados.
- Un empleado puede estar vinculado a un usuario.
- Un activo pertenece a una unidad organizacional.
- Un activo puede tener un empleado responsable.
- Un activo puede tener muchos adjuntos.
- Un activo puede tener muchos movimientos.
- Un activo puede tener muchos casos de mantenimiento.
- Una campaña puede incluir muchos activos.
- Un caso de mantenimiento puede pertenecer o no a una campaña.
- Un caso de mantenimiento puede tener muchos ítems de detalle.
- Un documento puede referenciar un activo, un mantenimiento, un movimiento o una campaña.
- Un dispositivo del agente puede vincularse a un activo.
- Un contexto de IA puede referenciar un activo, mantenimiento o campaña.

---

## 11. Requerimientos funcionales

## 11.1. Seguridad

- El sistema debe permitir autenticación de usuarios.
- El sistema debe permitir crear, editar y desactivar usuarios.
- El sistema debe permitir asignar roles.
- El sistema debe restringir vistas y acciones según rol.

## 11.2. Organización

- El sistema debe permitir registrar unidades organizacionales jerárquicas.
- El sistema debe permitir registrar empleados.
- El sistema debe permitir identificar técnicos.
- El sistema debe permitir registrar proveedores.

## 11.3. Activos

- El sistema debe permitir registrar activos manualmente.
- El sistema debe permitir editar datos del activo.
- El sistema debe permitir adjuntar archivos.
- El sistema debe permitir cambiar ubicación y responsable.
- El sistema debe permitir consultar historial del activo.
- El sistema debe permitir filtrar activos por estado, tipo, responsable, unidad y otros campos relevantes.

## 11.4. Mantenimiento individual

- El sistema debe permitir registrar incidencias o solicitudes.
- El sistema debe permitir abrir un caso de mantenimiento.
- El sistema debe permitir asignar un técnico.
- El sistema debe permitir registrar diagnóstico.
- El sistema debe permitir registrar acciones realizadas.
- El sistema debe permitir registrar tareas, repuestos y costos.
- El sistema debe permitir cerrar el mantenimiento.
- El sistema debe permitir programar el siguiente mantenimiento.

## 11.5. Campañas de mantenimiento

- El sistema debe permitir crear campañas.
- El sistema debe permitir seleccionar activos objetivo.
- El sistema debe permitir asignar técnicos a activos de campaña.
- El sistema debe permitir controlar el avance de cada activo.
- El sistema debe permitir cerrar la campaña con resumen final.

## 11.6. Documentos

- El sistema debe permitir generar ficha técnica.
- El sistema debe permitir generar acta de mantenimiento.
- El sistema debe permitir generar orden de trabajo.
- El sistema debe permitir generar constancia de entrega o devolución.
- El sistema debe permitir generar resumen de campaña.

## 11.7. Reportes

- El sistema debe mostrar reportes básicos en pantalla.
- El sistema debe permitir exportar listados.
- El sistema debe permitir consultar indicadores simples.

## 11.8. Auditoría

- El sistema debe registrar acciones importantes.
- El sistema debe registrar cambios relevantes sobre activos y mantenimientos.
- El sistema debe registrar generación documental.

## 11.9. Agente local

- El sistema debe permitir registrar equipos detectados.
- El sistema debe permitir recibir heartbeat.
- El sistema debe permitir vincular un equipo detectado con un activo.
- El sistema debe permitir visualizar cambios relevantes enviados por el agente.

## 11.10. IA opcional

- El sistema debe permitir registrar solicitudes asistidas.
- El sistema debe permitir mostrar respuestas sugeridas.
- El sistema no debe depender de la IA para operar.

---

## 12. Requerimientos no funcionales

- Seguridad basada en roles.
- Interfaz clara y usable.
- Mantenibilidad del código.
- Trazabilidad mínima garantizada.
- Escalabilidad progresiva.
- Posibilidad de crecimiento futuro sin rediseñar todo.
- Operación aun cuando fallen integraciones externas.

---

## 13. Flujos operativos principales

## 13.1. Flujo de registro de activo

1. Se crea el activo.
2. Se asigna ubicación organizacional.
3. Se asigna responsable.
4. Se adjuntan documentos o imágenes.
5. Se deja disponible para consulta y mantenimiento.

## 13.2. Flujo de movimiento o reasignación

1. Se selecciona el activo.
2. Se registra el tipo de movimiento.
3. Se indica origen y destino.
4. Se actualiza responsable o ubicación.
5. Se guarda el historial del movimiento.

## 13.3. Flujo de mantenimiento individual

1. Se registra el problema o solicitud.
2. Se crea el caso de mantenimiento.
3. Se asigna técnico.
4. Se registra diagnóstico.
5. Se registran acciones e ítems.
6. Se actualiza estado del activo si corresponde.
7. Se genera documento.
8. Se cierra el caso.

## 13.4. Flujo de campaña de mantenimiento

1. Se crea la campaña.
2. Se define alcance.
3. Se cargan activos objetivo.
4. Se asignan técnicos.
5. Se atienden activos.
6. Se relacionan mantenimientos con la campaña.
7. Se genera resumen final.
8. Se cierra la campaña.

## 13.5. Flujo del agente local

1. El equipo se registra o sincroniza.
2. Se envía heartbeat o snapshot.
3. El sistema procesa la información.
4. Se detectan cambios.
5. El equipo puede vincularse a un activo del inventario.

## 13.6. Flujo de IA opcional

1. El usuario solicita apoyo asistido.
2. El sistema arma el contexto.
3. Se envía prompt.
4. Se recibe respuesta.
5. Se muestra como sugerencia editable.
6. Se registra en el log de IA.

---

## 14. Documentos del sistema

### Documentos individuales

- ficha técnica del activo,
- acta de mantenimiento,
- orden de trabajo,
- constancia de entrega,
- constancia de devolución.

### Documentos consolidados

- resumen de campaña,
- relación de activos por área,
- relación de mantenimientos por periodo,
- relación de activos observados.

---

## 15. Reportes sugeridos para la primera versión

- activos por estado,
- activos por tipo,
- activos por unidad,
- activos por responsable,
- mantenimientos por técnico,
- mantenimientos por periodo,
- campañas por estado,
- costos de mantenimiento por periodo,
- activos sin atención reciente,
- equipos detectados por agente sin vincular.

---

## 16. Arquitectura técnica sugerida

## 16.1. Aplicación principal

- Laravel
- Blade o frontend simple según necesidad
- Base de datos relacional
- almacenamiento local o en servidor

## 16.2. Generación documental

- Blade + motor PDF

## 16.3. Reportes

- consultas SQL/Eloquent optimizadas
- exportación simple a Excel o PDF

## 16.4. Integración con agente

- API HTTP segura
- payload JSON
- autenticación por token de dispositivo

## 16.5. Integración con IA

- servicio externo desacoplado
- consumo vía HTTP API
- registro de interacción

---

## 17. Fases sugeridas de implementación

## Fase 1. Núcleo operativo

- Seguridad
- Organización
- Activos
- Movimientos
- Mantenimiento individual
- Auditoría básica

## Fase 2. Operación institucional

- Campañas de mantenimiento
- Documentos principales
- Reportes básicos
- Configuración
- Notificaciones simples

## Fase 3. Integraciones opcionales

- Agente local
- IA asistida

## Fase 4. Optimización futura

- dashboard más avanzado,
- automatizaciones,
- más indicadores,
- notificaciones más complejas,
- mayor granularidad de permisos.

---

## 18. Ventajas de esta versión simplificada

- Menor tiempo de desarrollo.
- Menor costo de mantenimiento.
- Menos migraciones y menos riesgo de inconsistencias.
- Más fácil de explicar al cliente.
- Más fácil de implementar en Laravel.
- Más fácil de evolucionar progresivamente.

---

## 19. Conclusión

La mejor decisión para construir este sistema pronto no es intentar implementar una arquitectura demasiado grande desde el inicio, sino construir una base simple, sólida y útil.

Por ello, esta nueva propuesta concentra el sistema en **5 módulos principales**:

- Seguridad
- Organización
- Activos
- Mantenimiento
- Soporte

Con esta estructura, el sistema sigue cumpliendo los requerimientos principales del negocio, pero reduce complejidad técnica, número de tablas, acoplamiento innecesario y tiempo de implementación.

Esta versión está pensada para ser el **MVP real y funcional** del SIGAT, sobre el cual luego se pueden agregar capacidades avanzadas sin rehacer todo el sistema.
