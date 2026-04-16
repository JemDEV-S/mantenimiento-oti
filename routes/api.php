<?php

use App\Http\Controllers\AgentDeviceController;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
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

    Route::get('organizational-units', function () {
        return OrganizationalUnit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    })->name('organizational-units.index');

    Route::get('employees/search', function () {
        $query = request('query', '');

        return Employee::query()
            ->with('organizationalUnit:id,name')
            ->active()
            ->search($query)
            ->orderBy('full_name')
            ->limit(20)
            ->get()
            ->map(fn (Employee $employee) => [
                'id' => $employee->id,
                'firstName' => $employee->name,
                'lastName' => $employee->last_name,
                'position' => $employee->position,
                'organizationalUnitName' => $employee->organizationalUnit?->name,
            ]);
    })->name('employees.search');

    // Endpoints autenticados por token del dispositivo
    Route::middleware('agent.auth')->group(function () {
        Route::post('agents/{uuid}/heartbeat', [AgentDeviceController::class, 'heartbeat'])->name('agents.heartbeat');
        Route::post('agents/{uuid}/sync', [AgentDeviceController::class, 'sync'])->name('agents.sync');
        Route::post('agents/{uuid}/snapshot', [AgentDeviceController::class, 'snapshot'])->name('agents.snapshot');
        Route::post('agents/{uuid}/bind-asset', [AgentDeviceController::class, 'bindAsset'])->name('agents.bind-asset');
    });
});
