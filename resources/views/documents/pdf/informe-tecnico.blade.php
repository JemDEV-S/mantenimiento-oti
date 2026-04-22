<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Informe Técnico {{ $case->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1e293b; background: #fff; }

        .page { padding: 18mm 16mm 14mm 16mm; }

        /* Header institucional */
        .header { display: table; width: 100%; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
        .header-left { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
        .entity-name { font-size: 10pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; }
        .entity-sub { font-size: 8pt; color: #475569; margin-top: 2px; }
        .doc-badge { display: inline-block; background: #1e3a5f; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 8pt; font-weight: bold; margin-bottom: 3px; }
        .doc-code { font-size: 8pt; color: #64748b; }

        /* Título del documento */
        .doc-title { text-align: center; margin: 10px 0 8px; }
        .doc-title h1 { font-size: 13pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { font-size: 8pt; color: #64748b; margin-top: 2px; }

        /* Secciones */
        .section { margin-bottom: 10px; }
        .section-title { background: #1e3a5f; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 3px 8px; margin-bottom: 6px; }
        .section-title-alt { background: #e2e8f0; color: #1e293b; font-size: 8.5pt; font-weight: bold; padding: 3px 8px; margin-bottom: 6px; }

        /* Grid de datos */
        .data-grid { display: table; width: 100%; border-collapse: collapse; }
        .data-row { display: table-row; }
        .data-label { display: table-cell; width: 30%; padding: 3px 6px; font-size: 8pt; color: #64748b; font-weight: bold; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .data-value { display: table-cell; padding: 3px 6px; font-size: 8.5pt; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: top; }

        /* Grid de 2 columnas */
        .two-col { display: table; width: 100%; }
        .col-half { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .col-half:last-child { padding-right: 0; padding-left: 8px; }

        /* Tabla de ítems */
        table.items { width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 4px; }
        table.items thead tr { background: #334155; color: #fff; }
        table.items thead th { padding: 4px 6px; text-align: left; font-weight: bold; }
        table.items tbody tr:nth-child(even) { background: #f8fafc; }
        table.items tbody td { padding: 3px 6px; border-bottom: 1px solid #e2e8f0; }
        table.items tfoot tr { background: #f1f5f9; font-weight: bold; }
        table.items tfoot td { padding: 4px 6px; border-top: 2px solid #334155; }

        /* Badge de estado */
        .badge { display: inline-block; padding: 1px 7px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        /* Caja de texto largo */
        .text-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 3px; padding: 6px 8px; font-size: 8.5pt; color: #334155; min-height: 22px; margin-bottom: 6px; }

        /* Área de firmas */
        .signatures { display: table; width: 100%; margin-top: 18px; }
        .sig-cell { display: table-cell; width: 33%; text-align: center; padding: 0 8px; }
        .sig-line { border-top: 1px solid #334155; margin-top: 28px; padding-top: 4px; font-size: 8pt; color: #334155; }
        .sig-label { font-size: 7.5pt; color: #64748b; }

        /* Footer */
        .footer { margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 5px; display: table; width: 100%; }
        .footer-left { display: table-cell; font-size: 7pt; color: #94a3b8; }
        .footer-right { display: table-cell; text-align: right; font-size: 7pt; color: #94a3b8; }

        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 8px 0; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .mt-4 { margin-top: 8px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="entity-name">Municipalidad Distrital de San Jerónimo</div>
            <div class="entity-sub">Oficina de Tecnologías de la Información</div>
        </div>
        <div class="header-right">
            <div class="doc-badge">INFORME TÉCNICO</div>
            <div class="doc-code">{{ $case->code }}</div>
            <div class="doc-code">Emitido: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Título --}}
    <div class="doc-title">
        <h1>Informe de Mantenimiento de Equipo TI</h1>
        <p>Sistema de Gestión de Activos y Mantenimiento — SIGAT</p>
    </div>

    {{-- Datos del caso --}}
    <div class="section">
        <div class="section-title">1. Datos del Caso</div>
        <div class="two-col">
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Código</div>
                        <div class="data-value fw-bold">{{ $case->code }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Tipo</div>
                        <div class="data-value">{{ $case->maintenance_type->label() }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Prioridad</div>
                        <div class="data-value">{{ $case->priority->label() }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Estado</div>
                        <div class="data-value">{{ $case->status->label() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Campaña</div>
                        <div class="data-value">{{ $case->campaign?->code ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Inicio</div>
                        <div class="data-value">{{ $case->started_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Cierre</div>
                        <div class="data-value">{{ $case->finished_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Costo total</div>
                        <div class="data-value fw-bold">{{ $case->total_cost ? 'S/ ' . number_format($case->total_cost, 2) : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Datos del activo --}}
    <div class="section">
        <div class="section-title">2. Activo Intervenido</div>
        @php $asset = $case->asset @endphp
        <div class="two-col">
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Nombre</div>
                        <div class="data-value fw-bold">{{ $asset?->name ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Código</div>
                        <div class="data-value">{{ $asset?->asset_code ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Tipo</div>
                        <div class="data-value">{{ $asset?->asset_type->label() ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Marca / Modelo</div>
                        <div class="data-value">{{ $asset?->brand ?? '—' }} {{ $asset?->model }}</div>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Serie</div>
                        <div class="data-value">{{ $asset?->serial_number ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Estado</div>
                        <div class="data-value">{{ $asset?->status->label() ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Unidad</div>
                        <div class="data-value">{{ $asset?->organizationalUnit?->name ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Responsable</div>
                        <div class="data-value">{{ $asset?->responsible?->full_name ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal --}}
    <div class="section">
        <div class="section-title">3. Personal Involucrado</div>
        <div class="two-col">
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Reportado por</div>
                        <div class="data-value">{{ $case->reportedBy?->full_name ?? '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Técnico asignado</div>
                        <div class="data-value fw-bold">{{ $case->assignedTechnician?->full_name ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Especialidad</div>
                        <div class="data-value">{{ $case->assignedTechnician?->specialty ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Descripción técnica --}}
    <div class="section">
        <div class="section-title">4. Descripción Técnica</div>

        <div class="section-title-alt">Descripción del problema</div>
        <div class="text-box">{{ $case->problem_description ?? 'No registrado.' }}</div>

        <div class="section-title-alt">Diagnóstico</div>
        <div class="text-box">{{ $case->diagnosis ?? 'No registrado.' }}</div>

        <div class="section-title-alt">Acciones realizadas</div>
        <div class="text-box">{{ $case->actions_taken ?? 'No registrado.' }}</div>

        @if ($case->notes)
        <div class="section-title-alt">Observaciones adicionales</div>
        <div class="text-box">{{ $case->notes }}</div>
        @endif
    </div>

    {{-- Ítems utilizados --}}
    @if ($case->items && $case->items->count() > 0)
    <div class="section">
        <div class="section-title">5. Materiales e Insumos Utilizados</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th style="text-align:center">Cant.</th>
                    <th style="text-align:right">P. Unit.</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($case->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->item_type->label() }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">S/ {{ number_format($item->unit_cost ?? 0, 2) }}</td>
                    <td style="text-align:right">S/ {{ number_format($item->total_cost ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right">Costo total:</td>
                    <td style="text-align:right">S/ {{ number_format($case->items->sum('total_cost'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Próximo mantenimiento --}}
    @if ($case->next_maintenance_date)
    <div class="section">
        <div class="section-title">6. Recomendaciones</div>
        <div class="data-grid">
            <div class="data-row">
                <div class="data-label">Próximo mantenimiento</div>
                <div class="data-value">{{ $case->next_maintenance_date->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Firmas --}}
    <div class="signatures">
        <div class="sig-cell">
            <div class="sig-line">{{ $case->assignedTechnician?->full_name ?? '___________________' }}</div>
            <div class="sig-label">Técnico responsable</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">{{ $case->conformity_name ?? '___________________' }}</div>
            <div class="sig-label">Conformidad del usuario</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">Jefe OTI</div>
            <div class="sig-label">Visto bueno</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">SIGAT — Sistema de Gestión de Activos y Mantenimiento · MDSJ</div>
        <div class="footer-right">Generado el {{ now()->format('d/m/Y H:i') }} · {{ $case->code }}</div>
    </div>

</div>
</body>
</html>
