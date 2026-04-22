<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ficha Técnica {{ $asset->asset_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1e293b; background: #fff; }
        .page { padding: 18mm 16mm 14mm 16mm; }

        .header { display: table; width: 100%; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
        .header-left { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
        .entity-name { font-size: 10pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; }
        .entity-sub { font-size: 8pt; color: #475569; margin-top: 2px; }
        .doc-badge { display: inline-block; background: #064e3b; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 8pt; font-weight: bold; margin-bottom: 3px; }
        .doc-code { font-size: 8pt; color: #64748b; }

        .doc-title { text-align: center; margin: 10px 0 8px; }
        .doc-title h1 { font-size: 13pt; font-weight: bold; color: #1e3a5f; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { font-size: 8pt; color: #64748b; margin-top: 2px; }

        /* Código del activo destacado */
        .asset-code-box { border: 2px solid #1e3a5f; border-radius: 4px; padding: 6px 14px; display: inline-block; margin-bottom: 10px; }
        .asset-code-box span { font-size: 14pt; font-weight: bold; color: #1e3a5f; letter-spacing: 2px; }
        .asset-code-box small { display: block; font-size: 7pt; color: #64748b; text-align: center; }

        .section { margin-bottom: 10px; }
        .section-title { background: #1e3a5f; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 3px 8px; margin-bottom: 5px; }
        .section-title-green { background: #064e3b; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 3px 8px; margin-bottom: 5px; }

        .data-grid { display: table; width: 100%; }
        .data-row { display: table-row; }
        .data-label { display: table-cell; width: 32%; padding: 3px 6px; font-size: 8pt; color: #64748b; font-weight: bold; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .data-value { display: table-cell; padding: 3px 6px; font-size: 8.5pt; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: top; }

        .two-col { display: table; width: 100%; }
        .col-half { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .col-half:last-child { padding-right: 0; padding-left: 8px; }

        /* Especificaciones técnicas */
        .spec-grid { display: table; width: 100%; }
        .spec-row { display: table-row; }
        .spec-label { display: table-cell; width: 40%; padding: 2px 6px; font-size: 7.5pt; color: #64748b; border-bottom: 1px solid #f1f5f9; }
        .spec-value { display: table-cell; padding: 2px 6px; font-size: 8pt; color: #1e293b; border-bottom: 1px solid #f1f5f9; font-weight: bold; }

        /* Software instalado */
        .software-list { columns: 2; column-gap: 10px; font-size: 7.5pt; color: #334155; }
        .software-item { padding: 1px 0; border-bottom: 1px dotted #e2e8f0; break-inside: avoid; }

        .badge { display: inline-block; padding: 1px 7px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        table.network { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.network thead tr { background: #334155; color: #fff; }
        table.network thead th { padding: 3px 6px; text-align: left; }
        table.network tbody td { padding: 3px 6px; border-bottom: 1px solid #e2e8f0; }
        table.network tbody tr:nth-child(even) { background: #f8fafc; }

        .footer { margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 5px; display: table; width: 100%; }
        .footer-left { display: table-cell; font-size: 7pt; color: #94a3b8; }
        .footer-right { display: table-cell; text-align: right; font-size: 7pt; color: #94a3b8; }

        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
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
            <div class="doc-badge">FICHA TÉCNICA</div>
            <div class="doc-code">{{ $asset->asset_code }}</div>
            <div class="doc-code">Emitido: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Título --}}
    <div class="doc-title">
        <h1>Ficha Técnica de Activo TI</h1>
        <p>{{ $asset->name }} — {{ $asset->asset_type->label() }}</p>
    </div>

    {{-- Identificación --}}
    <div class="section">
        <div class="section-title">1. Identificación del Activo</div>
        <div class="two-col">
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Nombre</div>
                        <div class="data-value fw-bold">{{ $asset->name }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Código</div>
                        <div class="data-value fw-bold">{{ $asset->asset_code }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">UUID</div>
                        <div class="data-value" style="font-size:7.5pt;word-break:break-all">{{ $asset->uuid }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Tipo</div>
                        <div class="data-value">{{ $asset->asset_type->label() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Estado</div>
                        <div class="data-value">{{ $asset->status->label() }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Condición</div>
                        <div class="data-value">{{ $asset->condition->label() }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Adquisición</div>
                        <div class="data-value">{{ $asset->purchase_date?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Valor ref.</div>
                        <div class="data-value">{{ $asset->reference_value ? 'S/ ' . number_format($asset->reference_value, 2) : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Descripción --}}
    <div class="section">
        <div class="section-title">2. Descripción del Equipo</div>
        <div class="two-col">
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Marca</div>
                        <div class="data-value">{{ $asset->brand ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Modelo</div>
                        <div class="data-value">{{ $asset->model ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">N° Serie</div>
                        <div class="data-value fw-bold">{{ $asset->serial_number ?? '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="data-grid">
                    <div class="data-row">
                        <div class="data-label">Unidad orgánica</div>
                        <div class="data-value fw-bold">{{ $asset->organizationalUnit?->name ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Responsable</div>
                        <div class="data-value">{{ $asset->responsible?->full_name ?? '—' }}</div>
                    </div>
                    <div class="data-row">
                        <div class="data-label">Ubicación</div>
                        <div class="data-value">{{ $asset->location ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @if ($asset->description)
        <div style="margin-top:5px;padding:5px 7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:3px;font-size:8.5pt;color:#334155">
            {{ $asset->description }}
        </div>
        @endif
    </div>

    {{-- Especificaciones técnicas --}}
    @php
        $specs = $asset->specs_json ?? [];
        $extra = $asset->extra_json ?? [];
        $agentSnap = $asset->agentDevice?->last_snapshot_json ?? [];
        $device = data_get($agentSnap, 'Device', data_get($agentSnap, 'device', []));
        $cpu = data_get($specs, 'cpu') ?? data_get($device, 'Cpu', data_get($device, 'cpu'));
        $ram = data_get($specs, 'ram') ?? data_get($device, 'RamTotal', data_get($device, 'ramTotal'));
        $os  = data_get($specs, 'os') ?? data_get($device, 'Os', data_get($device, 'os'));
        $hostname = data_get($specs, 'hostname') ?? data_get($device, 'Hostname', data_get($device, 'hostname'));
        $ipAddresses = data_get($specs, 'ip_addresses', data_get($device, 'IpAddresses', data_get($device, 'ipAddresses', [])));
        $storageVolumes = data_get($specs, 'storage_volumes', data_get($device, 'StorageVolumes', data_get($device, 'storageVolumes', [])));
        $topSoftware = collect(data_get($specs, 'top_software', []))->filter()->values();
        if ($topSoftware->isEmpty()) {
            $topSoftware = collect(data_get($agentSnap, 'InstalledSoftware', data_get($agentSnap, 'installedSoftware', [])))
                ->map(fn ($i) => data_get($i, 'Name', data_get($i, 'name')))
                ->filter()->take(20)->values();
        }
        $hasSpecs = $cpu || $ram || $os || $hostname || count($ipAddresses) > 0;
    @endphp

    @if ($hasSpecs)
    <div class="section">
        <div class="section-title">3. Especificaciones Técnicas</div>
        <div class="two-col">
            <div class="col-half">
                <div class="spec-grid">
                    @if ($hostname)
                    <div class="spec-row">
                        <div class="spec-label">Hostname</div>
                        <div class="spec-value">{{ $hostname }}</div>
                    </div>
                    @endif
                    @if ($os)
                    <div class="spec-row">
                        <div class="spec-label">Sistema operativo</div>
                        <div class="spec-value">{{ $os }}</div>
                    </div>
                    @endif
                    @if ($cpu)
                    <div class="spec-row">
                        <div class="spec-label">Procesador</div>
                        <div class="spec-value">{{ $cpu }}</div>
                    </div>
                    @endif
                    @if ($ram)
                    <div class="spec-row">
                        <div class="spec-label">Memoria RAM</div>
                        <div class="spec-value">{{ $ram }}</div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-half">
                @if (count($ipAddresses) > 0)
                <div class="spec-label" style="display:block;padding:0 0 3px 6px">Direcciones IP</div>
                <table class="network">
                    <thead><tr><th>IP</th><th>Descripción</th></tr></thead>
                    <tbody>
                        @foreach (array_slice((array) $ipAddresses, 0, 4) as $ip)
                        @php
                            $ipVal  = is_array($ip) ? (data_get($ip, 'Address', data_get($ip, 'address', $ip))) : $ip;
                            $ipDesc = is_array($ip) ? (data_get($ip, 'Description', data_get($ip, 'description', ''))) : '';
                        @endphp
                        <tr>
                            <td>{{ $ipVal }}</td>
                            <td>{{ $ipDesc }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        @if (count($storageVolumes) > 0)
        <div style="margin-top:5px">
            <div class="spec-label" style="display:block;padding:0 0 3px 0">Almacenamiento</div>
            <table class="network">
                <thead><tr><th>Unidad</th><th>Tamaño</th><th>Libre</th><th>% Uso</th></tr></thead>
                <tbody>
                    @foreach (array_slice((array) $storageVolumes, 0, 6) as $vol)
                    @php
                        $drive  = data_get($vol, 'DriveLetter', data_get($vol, 'driveLetter', data_get($vol, 'drive', '?')));
                        $size   = data_get($vol, 'TotalSize', data_get($vol, 'totalSize', '—'));
                        $free   = data_get($vol, 'FreeSpace', data_get($vol, 'freeSpace', '—'));
                        $usePct = data_get($vol, 'UsePercent', data_get($vol, 'usePercent', '—'));
                    @endphp
                    <tr>
                        <td>{{ $drive }}</td>
                        <td>{{ $size }}</td>
                        <td>{{ $free }}</td>
                        <td>{{ $usePct }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    {{-- Agente --}}
    @if ($asset->agentDevice)
    <div class="section">
        <div class="section-title-green">4. Agente de Monitoreo</div>
        <div class="data-grid">
            <div class="data-row">
                <div class="data-label">Estado del agente</div>
                <div class="data-value">{{ $asset->agentDevice->status->label() }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Último heartbeat</div>
                <div class="data-value">{{ $asset->agentDevice->last_seen_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Versión agente</div>
                <div class="data-value">{{ $asset->agentDevice->agent_version ?? '—' }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Software instalado --}}
    @if ($topSoftware->isNotEmpty())
    <div class="section">
        <div class="section-title">{{ $hasSpecs ? '5' : '4' }}. Software Instalado</div>
        <div class="software-list">
            @foreach ($topSoftware as $sw)
            <div class="software-item">{{ $sw }}</div>
            @endforeach
        </div>
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
