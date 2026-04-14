<?php

use App\Http\Controllers\AgentDeviceController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaintenanceCampaignController;
use App\Http\Controllers\MaintenanceCaseController;
use App\Http\Controllers\OrganizationalUnitController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Rutas privadas ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'user.active'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Administración de roles y permisos (solo admin) ──────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
        Route::resource('permissions', PermissionController::class)->except('show');
    });

    // ── Usuarios ─────────────────────────────────────────────────────────────
    Route::middleware('permission:user.view')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

    // ── Empleados ─────────────────────────────────────────────────────────────
    Route::middleware('permission:employee.view')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::patch('employees/{employee}/toggle-active', [EmployeeController::class, 'toggleActive'])->name('employees.toggle-active');
    });

    // ── Unidades organizacionales ─────────────────────────────────────────────
    Route::middleware('permission:org-unit.view')->group(function () {
        Route::resource('organizational-units', OrganizationalUnitController::class);
    });

    // ── Activos ───────────────────────────────────────────────────────────────
    Route::middleware('permission:asset.view')->group(function () {
        Route::resource('assets', AssetController::class);
    });

    // ── Movimientos de activos ────────────────────────────────────────────────
    Route::middleware('permission:asset-movement.view')->group(function () {
        Route::resource('asset-movements', AssetMovementController::class)->only(['index', 'create', 'store']);
    });

    // ── Campañas de mantenimiento ─────────────────────────────────────────────
    Route::middleware('permission:campaign.view')->group(function () {
        Route::resource('campaigns', MaintenanceCampaignController::class);
        Route::post('campaigns/{campaign}/assets', [MaintenanceCampaignController::class, 'addAsset'])->name('campaigns.assets.add');
        Route::delete('campaigns/{campaign}/assets/{asset}', [MaintenanceCampaignController::class, 'removeAsset'])->name('campaigns.assets.remove');
    });

    // ── Casos de mantenimiento ────────────────────────────────────────────────
    Route::middleware('permission:maintenance-case.view')->group(function () {
        Route::resource('maintenance-cases', MaintenanceCaseController::class);
        Route::patch('maintenance-cases/{maintenanceCase}/close', [MaintenanceCaseController::class, 'close'])->name('maintenance-cases.close');
        Route::post('maintenance-cases/{maintenanceCase}/items', [MaintenanceCaseController::class, 'addItem'])->name('maintenance-cases.items.add');
        Route::delete('maintenance-cases/{maintenanceCase}/items/{item}', [MaintenanceCaseController::class, 'removeItem'])->name('maintenance-cases.items.remove');
    });

    // ── Documentos ────────────────────────────────────────────────────────────
    Route::middleware('permission:document.view')->group(function () {
        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // ── Agentes (vista admin) ─────────────────────────────────────────────────
    Route::middleware('permission:agent.view')->group(function () {
        Route::get('agents', [AgentDeviceController::class, 'webIndex'])->name('agents.index');
        Route::get('agents/{agentDevice}', [AgentDeviceController::class, 'webShow'])->name('agents.show');
    });

    // ── Configuración ─────────────────────────────────────────────────────────
    Route::middleware('permission:setting.view')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::patch('settings/{setting}', [SettingController::class, 'update'])->name('settings.update');
    });
});
