<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Historial de Mantenimiento {{ $asset->asset_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color: #1e293b; background: #fff; }
        .page { padding: 16mm 14mm 14mm 14mm; }

        .header { display: table; width: 100%; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
        .header-left { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
        .entity-name { font-size: 10pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; }
        .entity-sub { font-size: 8pt; color: #475569; margin-top: 2px; }
        .doc-badge { display: inline-block; background: #581c87; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 8pt; font-weight: bold; margin-bottom: 3px; }
        .doc-code { font-size: 8pt; color: #64748b; }

        .doc-title { text-align: center; margin: 8px 0 6px; }
        .doc-title h1 { font-size: 12pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { font-size: 8pt; color: #64748b; margin-top: 2px; }

        /* Resumen del activo */
        .asset-summary { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 4px; padding: 7px 10px; margin-bottom: 9px; display: table; width: 100%; }
        .asset-summary-col { display: table-cell; width: 33%; vertical-align: top; }
        .asset-summary-label { font-size: 7.5pt; color: #0369a1; font-weight: bold; }
        .asset-summary-value { font-size: 9pt; color: #0c4a6e; font-weight: bold; }

        .section { margin-bottom: 9px; }
        .section-title { background: #1e3a5f; color: #fff; font-size: 8pt; font-weight: bold; padding: 3px 8px; margin-bottom: 5px; }

        /* Tabla principal de historial */
        table.history { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.history thead tr { background: #1e3a5f; color: #fff; }
        table.history thead th { padding: 4px 5px; text-align: left; font-weight: bold; }
        table.history tbody tr:nth-child(even) { background: #f8fafc; }
        table.history tbody td { padding: 4px 5px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        table.history tfoot tr { background: #f1f5f9; }
        table.history tfoot td { padding: 4px 5px; font-weight: bold; border-top: 2px solid #1e3a5f; }

        /* Detalle expandido del caso */
        .case-detail { background: #fafafa; border-left: 3px solid #1e3a5f; padding: 4px 7px; margin: 2px 0; font-size: 7.5pt; color: #334155; }
        .case-detail-label { font-weight: bold; color: #64748b; }

        /* Items sub-tabla */
        table.items-mini { width: 100%; border-collapse: collapse; font-size: 7pt; margin-top: 3px; }
        table.items-mini thead tr { background: #e2e8f0; }
        table.items-mini thead th { padding: 2px 4px; text-align: left; }
        table.items-mini tbody td { padding: 2px 4px; border-bottom: 1px dotted #e2e8f0; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 2px; font-size: 7pt; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        /* Estadísticas */
        .stats-row { display: table; width: 100%; margin-bottom: 8px; }
        .stat-box { display: table-cell; text-align: center; padding: 5px 8px; }
        .stat-box:not(:last-child) { border-right: 1px solid #e2e8f0; }
        .stat-num { font-size: 14pt; font-weight: bold; color: #1e3a5f; }
        .stat-label { font-size: 7.5pt; color: #64748b; }

        .footer { margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 4px; display: table; width: 100%; }
        .footer-left { display: table-cell; font-size: 7pt; color: #94a3b8; }
        .footer-right { display: table-cell; text-align: right; font-size: 7pt; color: #94a3b8; }

        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
            <div class="doc-badge">HISTORIAL DE MANTENIMIENTO</div>
            <div class="doc-code">{{ $asset->asset_code }}</div>
            <div class="doc-code">Al: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Título --}}
    <div class="doc-title">
        <h1>Historial de Mantenimiento</h1>
        <p>{{ $asset->name }} — {{ $asset->asset_type->label() }}</p>
    </div>

    {{-- Resumen del activo --}}
    <div class="asset-summary">
        <div class="asset-summary-col">
            <div class="asset-summary-label">Activo</div>
            <div class="asset-summary-value">{{ $asset->asset_code }}</div>
            <div style="font-size:8pt;color:#0369a1">{{ $asset->name }}</div>
        </div>
        <div class="asset-summary-col">
            <div class="asset-summary-label">Equipo</div>
            <div class="asset-summary-value">{{ $asset->brand ?? '—' }} {{ $asset->model }}</div>
            <div style="font-size:8pt;color:#0369a1">S/N: {{ $asset->serial_number ?? '—' }}</div>
        </div>
        <div class="asset-summary-col">
            <div class="asset-summary-label">Ubicación</div>
            <div class="asset-summary-value" style="font-size:8.5pt">{{ $asset->organizationalUnit?->name ?? '—' }}</div>
            <div style="font-size:8pt;color:#0369a1">{{ $asset->responsible?->full_name ?? '—' }}</div>
        </div>
    </div>

    {{-- Estadísticas --}}
    @php
        $cases = $asset->maintenanceCases;
        $totalCases      = $cases->count();
        $completedCases  = $cases->filter(fn ($c) => $c->status->value === 'completado')->count();
        $totalCost       = $cases->sum('total_cost');
        $lastCase        = $cases->sortByDesc('created_at')->first();
        $preventivos     = $cases->filter(fn ($c) => $c->maintenance_type->value === 'preventivo')->count();
        $correctivos     = $cases->filter(fn ($c) => $c->maintenance_type->value === 'correctivo')->count();
    @endphp

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-num">{{ $totalCases }}</div>
            <div class="stat-label">Total de casos</div>
        </div>
        <div class="stat-box">
            <div class="stat-num">{{ $completedCases }}</div>
            <div class="stat-label">Completados</div>
        </div>
        <div class="stat-box">
            <div class="stat-num">{{ $preventivos }}</div>
            <div class="stat-label">Preventivos</div>
        </div>
        <div class="stat-box">
            <div class="stat-num">{{ $correctivos }}</div>
            <div class="stat-label">Correctivos</div>
        </div>
        <div class="stat-box">
            <div class="stat-num">{{ $totalCost > 0 ? 'S/' . number_format($totalCost, 0) : '—' }}</div>
            <div class="stat-label">Costo acumulado</div>
        </div>
    </div>

    {{-- Tabla de historial --}}
    <div class="section">
        <div class="section-title">Registro Cronológico de Mantenimientos</div>

        @if ($cases->isEmpty())
        <div style="padding:10px;text-align:center;color:#94a3b8;font-style:italic;background:#f8fafc;border:1px solid #e2e8f0;border-radius:3px">
            No se han registrado casos de mantenimiento para este activo.
        </div>
        @else
        <table class="history">
            <thead>
                <tr>
                    <th style="width:12%">Código</th>
                    <th style="width:8%">Fecha</th>
                    <th style="width:10%">Tipo</th>
                    <th style="width:8%">Prioridad</th>
                    <th style="width:12%">Técnico</th>
                    <th style="width:8%">Estado</th>
                    <th style="width:8%">Costo</th>
                    <th style="width:34%">Descripción / Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cases as $case)
                @php
                    $statusTone = match($case->status->value) {
                        'completado' => 'success', 'cancelado' => 'danger',
                        'en_progreso' => 'warning', 'en_espera' => 'warning',
                        default => 'neutral',
                    };
                    $typeTone = match($case->maintenance_type->value) {
                        'preventivo' => 'info', 'correctivo' => 'warning',
                        'emergencia' => 'danger', default => 'neutral',
                    };
                @endphp
                <tr>
                    <td class="fw-bold">{{ $case->code }}</td>
                    <td>{{ $case->created_at->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $typeTone }}">{{ $case->maintenance_type->label() }}</span></td>
                    <td>{{ $case->priority->label() }}</td>
                    <td>{{ $case->assignedTechnician?->full_name ?? '—' }}</td>
                    <td><span class="badge badge-{{ $statusTone }}">{{ $case->status->label() }}</span></td>
                    <td class="text-right">{{ $case->total_cost ? 'S/ ' . number_format($case->total_cost, 2) : '—' }}</td>
                    <td>
                        @if ($case->problem_description)
                        <span class="text-muted">Prob: </span>{{ Str::limit($case->problem_description, 80) }}
                        @endif
                        @if ($case->actions_taken)
                        <br><span class="text-muted">Acc: </span>{{ Str::limit($case->actions_taken, 80) }}
                        @endif
                        @if ($case->campaign)
                        <br><span class="text-muted">Campaña: </span>{{ $case->campaign->code }}
                        @endif
                    </td>
                </tr>

                {{-- Items del caso --}}
                @if ($case->items && $case->items->count() > 0)
                <tr>
                    <td colspan="8" style="padding:0 5px 5px 20px;background:#fafafa">
                        <table class="items-mini">
                            <thead>
                                <tr>
                                    <th colspan="4" style="color:#334155;background:#e2e8f0;padding:2px 4px">
                                        Materiales e insumos ({{ $case->items->count() }} ítem{{ $case->items->count() > 1 ? 's' : '' }})
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width:50%">Descripción</th>
                                    <th style="width:15%">Tipo</th>
                                    <th style="width:10%;text-align:center">Cant.</th>
                                    <th style="width:25%;text-align:right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($case->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->item_type->label() }}</td>
                                    <td style="text-align:center">{{ $item->quantity }}</td>
                                    <td style="text-align:right">S/ {{ number_format($item->total_cost ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            @if ($totalCost > 0)
            <tfoot>
                <tr>
                    <td colspan="6">Total acumulado en mantenimientos</td>
                    <td class="text-right">S/ {{ number_format($totalCost, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
        @endif
    </div>

    {{-- Próximo mantenimiento --}}
    @php
        $nextMaint = $cases->filter(fn ($c) => $c->next_maintenance_date)
            ->sortByDesc('next_maintenance_date')->first();
    @endphp
    @if ($nextMaint?->next_maintenance_date)
    <div style="padding:5px 8px;background:#fef3c7;border:1px solid #fcd34d;border-radius:3px;font-size:8pt;color:#92400e">
        <strong>Próximo mantenimiento recomendado:</strong> {{ $nextMaint->next_maintenance_date->format('d/m/Y') }}
        (según caso {{ $nextMaint->code }})
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">SIGAT — Sistema de Gestión de Activos y Mantenimiento · MDSJ</div>
        <div class="footer-right">Generado el {{ now()->format('d/m/Y H:i') }} · {{ $asset->asset_code }}</div>
    </div>

</div>
</body>
</html>
