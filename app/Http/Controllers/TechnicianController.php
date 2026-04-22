<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\MaintenanceCaseStatus;
use App\Enums\MaintenanceItemType;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\MaintenanceCase;
use App\Services\Technician\TechnicianService;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function __construct(
        private readonly TechnicianService $technicianService,
    ) {}

    public function dashboard()
    {
        $employee = auth()->user()->employee;

        $data = $this->technicianService->getDashboardData($employee);

        return view('technicians.dashboard', array_merge(['employee' => $employee], $data));
    }

    public function workQueue()
    {
        $employee = auth()->user()->employee;

        $queues = $this->technicianService->getWorkQueueData($employee);

        return view('technicians.work-queue', compact('employee', 'queues'));
    }

    public function attendAsset()
    {
        $formData = $this->technicianService->getAttendAssetFormData();

        $preselectedAsset = request()->has('asset_id')
            ? Asset::find(request()->integer('asset_id'))
            : null;

        return view('technicians.attend-asset', array_merge($formData, [
            'types'           => MaintenanceType::cases(),
            'priorities'      => MaintenancePriority::cases(),
            'itemTypes'       => MaintenanceItemType::cases(),
            'employee'        => auth()->user()->employee,
            'preselectedAsset' => $preselectedAsset,
        ]));
    }

    public function caseDetail(MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.view');

        $maintenanceCase->load('asset', 'asset.organizationalUnit', 'campaign', 'reportedBy', 'assignedTechnician', 'items', 'documents');

        $itemTypes    = MaintenanceItemType::cases();
        $documentTypes = DocumentType::cases();
        $statuses     = MaintenanceCaseStatus::cases();
        $isClosed     = in_array($maintenanceCase->status->value, ['completado', 'cancelado']);

        return view('technicians.case-detail', compact(
            'maintenanceCase', 'itemTypes', 'documentTypes', 'statuses', 'isClosed'
        ));
    }

    public function updateProgress(Request $request, MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.edit');

        $data = $request->validate([
            'status'        => ['nullable', 'in:pendiente,en_progreso,en_espera'],
            'diagnosis'     => ['nullable', 'string', 'max:2000'],
            'actions_taken' => ['nullable', 'string', 'max:2000'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $this->technicianService->updateProgress($maintenanceCase, $data);

        return back()->with('success', 'Caso actualizado correctamente.');
    }

    public function storeAttention(Request $request)
    {
        $this->authorize('maintenance-case.create');

        $data = $request->validate([
            'asset_id'            => ['required', 'integer', 'exists:assets,id'],
            'maintenance_type'    => ['required', 'string'],
            'priority'            => ['required', 'string'],
            'problem_description' => ['required', 'string', 'max:2000'],
            'diagnosis'           => ['nullable', 'string', 'max:2000'],
            'actions_taken'       => ['nullable', 'string', 'max:2000'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'final_status'        => ['required', 'in:pendiente,en_progreso,completado'],
            'items'               => ['nullable', 'array'],
            'items.*.item_type'   => ['required_with:items', 'string'],
            'items.*.name'        => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity'    => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_cost'   => ['nullable', 'numeric', 'min:0'],
        ]);

        $case = $this->technicianService->registerAttention($data, auth()->user());

        return redirect()
            ->route('maintenance-cases.show', $case)
            ->with('success', "Caso {$case->code} registrado correctamente.");
    }
}
