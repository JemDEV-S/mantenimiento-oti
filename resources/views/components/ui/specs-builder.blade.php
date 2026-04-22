@props([
    'name'          => 'specs_json',
    'value'         => [],
    'assetType'     => '',   // valor inicial del tipo de activo (para edit o old())
    'watchEvent'    => 'asset-type-change',  // evento custom que dispara el select de tipo
])

@php
$initialType   = old('asset_type', $assetType ?? '');
$initialValues = old($name, $value ?? []);

$templates = [
    'computadora' => [
        ['key' => 'cpu',         'label' => 'Procesador',             'placeholder' => 'Ej: Intel Core i5-12400'],
        ['key' => 'ram',         'label' => 'Memoria RAM',            'placeholder' => 'Ej: 8 GB DDR4'],
        ['key' => 'storage',     'label' => 'Almacenamiento',         'placeholder' => 'Ej: 512 GB SSD'],
        ['key' => 'os',          'label' => 'Sistema operativo',      'placeholder' => 'Ej: Windows 11 Pro'],
        ['key' => 'ip_address',  'label' => 'Dirección IP',           'placeholder' => 'Ej: 192.168.1.100'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',          'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
    ],
    'laptop' => [
        ['key' => 'cpu',         'label' => 'Procesador',             'placeholder' => 'Ej: Intel Core i7-1255U'],
        ['key' => 'ram',         'label' => 'Memoria RAM',            'placeholder' => 'Ej: 16 GB DDR5'],
        ['key' => 'storage',     'label' => 'Almacenamiento',         'placeholder' => 'Ej: 1 TB NVMe SSD'],
        ['key' => 'os',          'label' => 'Sistema operativo',      'placeholder' => 'Ej: Windows 11 Pro'],
        ['key' => 'screen_size', 'label' => 'Tamaño de pantalla',     'placeholder' => 'Ej: 14 pulgadas'],
        ['key' => 'ip_address',  'label' => 'Dirección IP',           'placeholder' => 'Ej: 192.168.1.100'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',          'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
    ],
    'servidor' => [
        ['key' => 'cpu',         'label' => 'Procesador',             'placeholder' => 'Ej: Intel Xeon E-2300'],
        ['key' => 'ram',         'label' => 'Memoria RAM',            'placeholder' => 'Ej: 32 GB ECC'],
        ['key' => 'storage',     'label' => 'Almacenamiento',         'placeholder' => 'Ej: 2 × 1 TB RAID 1'],
        ['key' => 'os',          'label' => 'Sistema operativo',      'placeholder' => 'Ej: Ubuntu Server 22.04'],
        ['key' => 'ip_address',  'label' => 'Dirección IP',           'placeholder' => 'Ej: 192.168.1.10'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',          'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
    ],
    'impresora' => [
        ['key' => 'ip_address',  'label' => 'Dirección IP',           'placeholder' => 'Ej: 192.168.1.50'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',          'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'protocol',    'label' => 'Protocolo de conexión',  'placeholder' => 'Ej: LAN, Wi-Fi, USB'],
        ['key' => 'toner_model', 'label' => 'Modelo de tóner',        'placeholder' => 'Ej: HP CE285A'],
        ['key' => 'print_speed', 'label' => 'Velocidad de impresión', 'placeholder' => 'Ej: 30 ppm'],
    ],
    'escaner' => [
        ['key' => 'ip_address',  'label' => 'Dirección IP',           'placeholder' => 'Ej: 192.168.1.51'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',          'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'resolution',  'label' => 'Resolución máxima',      'placeholder' => 'Ej: 1200 DPI'],
        ['key' => 'protocol',    'label' => 'Protocolo de conexión',  'placeholder' => 'Ej: LAN, USB'],
    ],
    'monitor' => [
        ['key' => 'screen_size',    'label' => 'Tamaño de pantalla',          'placeholder' => 'Ej: 24 pulgadas'],
        ['key' => 'resolution',     'label' => 'Resolución',                  'placeholder' => 'Ej: 1920 × 1080'],
        ['key' => 'panel_type',     'label' => 'Tipo de panel',               'placeholder' => 'Ej: IPS, TN, VA'],
        ['key' => 'ports',          'label' => 'Puertos disponibles',         'placeholder' => 'Ej: HDMI, VGA, DisplayPort'],
        ['key' => 'refresh_rate',   'label' => 'Frecuencia de actualización', 'placeholder' => 'Ej: 60 Hz'],
    ],
    'tablet' => [
        ['key' => 'os',          'label' => 'Sistema operativo', 'placeholder' => 'Ej: Android 13, iPadOS 17'],
        ['key' => 'storage',     'label' => 'Almacenamiento',    'placeholder' => 'Ej: 64 GB'],
        ['key' => 'screen_size', 'label' => 'Tamaño de pantalla','placeholder' => 'Ej: 10.1 pulgadas'],
        ['key' => 'imei',        'label' => 'IMEI',              'placeholder' => 'Ej: 123456789012345'],
    ],
    'telefono_ip' => [
        ['key' => 'ip_address',  'label' => 'Dirección IP',  'placeholder' => 'Ej: 192.168.1.200'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC', 'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'extension',   'label' => 'Extensión',     'placeholder' => 'Ej: 1023'],
        ['key' => 'sip_user',    'label' => 'Usuario SIP',   'placeholder' => 'Ej: 1023@pbx.local'],
    ],
    'proyector' => [
        ['key' => 'resolution',  'label' => 'Resolución',           'placeholder' => 'Ej: 1920 × 1080'],
        ['key' => 'lumens',      'label' => 'Lúmenes',              'placeholder' => 'Ej: 3500 ANSI lm'],
        ['key' => 'input_ports', 'label' => 'Entradas disponibles', 'placeholder' => 'Ej: HDMI, VGA, USB'],
        ['key' => 'lamp_hours',  'label' => 'Horas de lámpara',     'placeholder' => 'Ej: 5000 h'],
    ],
    'ups' => [
        ['key' => 'capacity_va',    'label' => 'Capacidad (VA)',     'placeholder' => 'Ej: 1000 VA'],
        ['key' => 'capacity_watts', 'label' => 'Potencia (W)',       'placeholder' => 'Ej: 600 W'],
        ['key' => 'battery_model',  'label' => 'Modelo de batería',  'placeholder' => 'Ej: RBC17'],
        ['key' => 'runtime',        'label' => 'Autonomía estimada', 'placeholder' => 'Ej: 15 min al 50%'],
    ],
    'switch' => [
        ['key' => 'ip_address',   'label' => 'Dirección IP',        'placeholder' => 'Ej: 192.168.1.1'],
        ['key' => 'mac_address',  'label' => 'Dirección MAC',       'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'ports_count',  'label' => 'Número de puertos',   'placeholder' => 'Ej: 24'],
        ['key' => 'speed',        'label' => 'Velocidad de puertos','placeholder' => 'Ej: 1 Gbps'],
        ['key' => 'vlan',         'label' => 'Soporte VLAN',        'placeholder' => 'Ej: Sí, hasta 256 VLANs'],
    ],
    'router' => [
        ['key' => 'ip_address',  'label' => 'IP LAN',        'placeholder' => 'Ej: 192.168.1.1'],
        ['key' => 'wan_ip',      'label' => 'IP WAN',        'placeholder' => 'Ej: 200.48.x.x'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC', 'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'speed',       'label' => 'Velocidad WAN', 'placeholder' => 'Ej: 100 Mbps'],
    ],
    'access_point' => [
        ['key' => 'ip_address',  'label' => 'Dirección IP',  'placeholder' => 'Ej: 192.168.1.2'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC', 'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'ssid',        'label' => 'SSID',          'placeholder' => 'Ej: MDSJ-WiFi'],
        ['key' => 'frequency',   'label' => 'Frecuencia',    'placeholder' => 'Ej: 2.4 GHz / 5 GHz'],
        ['key' => 'standard',    'label' => 'Estándar Wi-Fi','placeholder' => 'Ej: 802.11ac (Wi-Fi 5)'],
    ],
    'camara' => [
        ['key' => 'ip_address',  'label' => 'Dirección IP',      'placeholder' => 'Ej: 192.168.2.10'],
        ['key' => 'mac_address', 'label' => 'Dirección MAC',     'placeholder' => 'Ej: AA:BB:CC:DD:EE:FF'],
        ['key' => 'resolution',  'label' => 'Resolución',        'placeholder' => 'Ej: 2 MP (1080p)'],
        ['key' => 'location',    'label' => 'Ubicación física',  'placeholder' => 'Ej: Entrada principal'],
        ['key' => 'nvr_channel', 'label' => 'Canal NVR/DVR',    'placeholder' => 'Ej: Canal 4'],
    ],
    'disco_externo' => [
        ['key' => 'capacity',   'label' => 'Capacidad',              'placeholder' => 'Ej: 1 TB'],
        ['key' => 'interface',  'label' => 'Interfaz',               'placeholder' => 'Ej: USB 3.0, USB-C'],
        ['key' => 'filesystem', 'label' => 'Sistema de archivos',    'placeholder' => 'Ej: NTFS, exFAT'],
    ],
];
@endphp

<div
    x-data="{
        type: '{{ $initialType }}',
        values: {{ Js::from((object) $initialValues) }},
        templates: {{ Js::from($templates) }},
        get fields() {
            return this.templates[this.type] ?? [];
        },
        get hasFields() {
            return this.fields.length > 0;
        },
        onTypeChange(newType) {
            if (this.type !== newType) {
                this.values = {};
            }
            this.type = newType;
        },
        getValue(key) {
            return this.values[key] ?? '';
        },
        inputName(key) {
            return '{{ $name }}[' + key + ']';
        },
        init() {
            window.addEventListener('{{ $watchEvent }}', (e) => {
                this.onTypeChange(e.detail.type);
            });
        }
    }"
    x-show="hasFields"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
>
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-slate-100 bg-slate-50/60">
            <svg class="w-4 h-4 text-sigat-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
            <h3 class="text-sm font-semibold text-slate-900">Especificaciones técnicas</h3>
            <span class="ml-auto text-xs text-slate-400">Todos los campos son opcionales</span>
        </div>

        {{-- Grid de campos --}}
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <template x-for="field in fields" :key="field.key">
                    <div class="space-y-1.5">
                        <label :for="'spec_' + field.key" class="block text-sm font-medium text-slate-700" x-text="field.label"></label>
                        <input
                            :id="'spec_' + field.key"
                            :name="inputName(field.key)"
                            type="text"
                            :placeholder="field.placeholder"
                            :value="getValue(field.key)"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-colors focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none"
                        >
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
