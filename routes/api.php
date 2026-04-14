<?php

use App\Http\Controllers\AgentDeviceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Agent endpoints
|--------------------------------------------------------------------------
|
| Estos endpoints son consumidos por el agente de monitoreo instalado en
| los activos. La autenticación se realiza mediante un Bearer token que
| se genera al registrar el agente (campo api_token en agent_devices).
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Registro de nuevo agente (requiere clave de instalación definida en .env APP_AGENT_INSTALL_KEY)
    Route::post('agents/register', [AgentDeviceController::class, 'register'])->name('agents.register');

    // Endpoints autenticados por token del dispositivo
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('agents/{uuid}/heartbeat', [AgentDeviceController::class, 'heartbeat'])->name('agents.heartbeat');
        Route::post('agents/{uuid}/sync', [AgentDeviceController::class, 'sync'])->name('agents.sync');
    });
});
