<?php

namespace App\Http\Controllers;

use App\DTOs\AgentDevice\HeartbeatDTO;
use App\DTOs\AgentDevice\RegisterAgentDTO;
use App\DTOs\AgentSync\CreateAgentSyncDTO;
use App\Http\Requests\Agent\HeartbeatRequest;
use App\Http\Requests\Agent\RegisterAgentRequest;
use App\Http\Requests\Agent\SyncRequest;
use App\Models\AgentDevice;
use App\Models\Asset;
use App\Services\AgentDevice\AgentDeviceService;
use Illuminate\Http\JsonResponse;

class AgentDeviceController extends Controller
{
    public function __construct(
        private readonly AgentDeviceService $agentDeviceService,
    ) {}

    /**
     * Registro de un nuevo agente (llamado desde el cliente agente con clave de instalación).
     */
    public function register(RegisterAgentRequest $request): JsonResponse
    {
        $asset = Asset::where('internal_code', $request->validated()['asset_internal_code'])->firstOrFail();

        $dto = RegisterAgentDTO::fromArray($request->validated());

        $device = $this->agentDeviceService->register($asset, $dto);

        return response()->json([
            'message'    => 'Agente registrado correctamente.',
            'uuid'       => $device->uuid,
            'api_token'  => $device->api_token,  // Solo se devuelve en el registro
        ], 201);
    }

    /**
     * Heartbeat periódico del agente.
     */
    public function heartbeat(HeartbeatRequest $request, string $uuid): JsonResponse
    {
        $device = AgentDevice::where('uuid', $uuid)->firstOrFail();

        $this->agentDeviceService->heartbeat(
            $device,
            HeartbeatDTO::fromArray($request->validated())
        );

        return response()->json(['message' => 'OK']);
    }

    /**
     * Envío de datos de sincronización (snapshot o delta).
     */
    public function sync(SyncRequest $request, string $uuid): JsonResponse
    {
        $device = AgentDevice::where('uuid', $uuid)->firstOrFail();

        $dto = CreateAgentSyncDTO::fromArray(array_merge(
            $request->validated(),
            ['agent_device_id' => $device->id]
        ));

        $sync = $this->agentDeviceService->receiveSync($device, $dto);

        return response()->json([
            'message' => 'Sincronización recibida.',
            'sync_id' => $sync->id,
        ]);
    }

    // ── Web / Admin ──────────────────────────────────────────────

    public function webIndex()
    {
        $this->authorize('agent.view');

        $devices = $this->agentDeviceService->getPaginated(
            request()->only(['status'])
        );

        return view('agents.index', compact('devices'));
    }

    public function webShow(AgentDevice $agentDevice)
    {
        $this->authorize('agent.view');

        $agentDevice->load('asset', 'syncs');

        return view('agents.show', compact('agentDevice'));
    }
}
