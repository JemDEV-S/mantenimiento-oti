<?php

namespace App\Http\Middleware;

use App\Services\AgentDevice\AgentDeviceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAgent
{
    public function __construct(
        private readonly AgentDeviceService $agentDeviceService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            abort(Response::HTTP_UNAUTHORIZED, 'Token de agente requerido.');
        }

        $device = $this->agentDeviceService->findByToken($token);
        $routeUuid = $request->route('uuid');

        if ($routeUuid && $device->uuid !== $routeUuid) {
            abort(Response::HTTP_FORBIDDEN, 'El token no corresponde al agente solicitado.');
        }

        $request->attributes->set('agent_device', $device);

        return $next($request);
    }
}
