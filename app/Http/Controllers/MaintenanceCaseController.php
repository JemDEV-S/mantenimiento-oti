<?php

namespace App\Http\Controllers;

use App\DTOs\MaintenanceCase\CreateMaintenanceCaseDTO;
use App\DTOs\MaintenanceCase\UpdateMaintenanceCaseDTO;
use App\DTOs\MaintenanceItem\CreateMaintenanceItemDTO;
use App\Enums\MaintenanceCaseStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceType;
use App\Http\Requests\MaintenanceCase\CloseMaintenanceCaseRequest;
use App\Http\Requests\MaintenanceCase\StoreMaintenanceCaseRequest;
use App\Http\Requests\MaintenanceCase\UpdateMaintenanceCaseRequest;
use App\Http\Requests\MaintenanceItem\StoreMaintenanceItemRequest;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\MaintenanceCase;
use App\Services\MaintenanceCase\MaintenanceCaseService;

class MaintenanceCaseController extends Controller
{
    public function __construct(
        private readonly MaintenanceCaseService $caseService,
    ) {}

    public function index()
    {
        $this->authorize('maintenance-case.view');

        $cases = $this->caseService->getPaginated(request()->only([
            'search', 'status', 'type', 'technician_id', 'asset_id', 'campaign_id',
        ]));

        $types      = MaintenanceType::cases();
        $statuses   = MaintenanceCaseStatus::cases();
        $technicians = Employee::technicians()->orderBy('full_name')->get();

        return view('maintenance-cases.index', compact('cases', 'types', 'statuses', 'technicians'));
    }

    public function create()
    {
        $this->authorize('maintenance-case.create');

        $assets      = Asset::orderBy('name')->get();
        $technicians = Employee::technicians()->orderBy('full_name')->get();
        $employees   = Employee::where('is_active', true)->orderBy('full_name')->get();
        $types       = MaintenanceType::cases();
        $priorities  = MaintenancePriority::cases();

        return view('maintenance-cases.create', compact('assets', 'technicians', 'employees', 'types', 'priorities'));
    }

    public function store(StoreMaintenanceCaseRequest $request)
    {
        $data = array_merge($request->validated(), [
            'created_by'             => auth()->id(),
            'reported_by_employee_id' => $request->validated()['reported_by_employee_id']
                                        ?? auth()->user()->employee_id,
        ]);

        $case = $this->caseService->create(
            CreateMaintenanceCaseDTO::fromArray($data)
        );

        return redirect()->route('maintenance-cases.show', $case)
            ->with('success', 'Caso de mantenimiento creado correctamente.');
    }

    public function show(MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.view');

        $maintenanceCase->load('asset', 'campaign', 'reportedBy', 'assignedTechnician', 'items', 'documents');

        return view('maintenance-cases.show', compact('maintenanceCase'));
    }

    public function edit(MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.edit');

        $technicians = Employee::technicians()->orderBy('full_name')->get();
        $types       = MaintenanceType::cases();
        $priorities  = MaintenancePriority::cases();
        $statuses    = MaintenanceCaseStatus::cases();

        return view('maintenance-cases.edit', compact('maintenanceCase', 'technicians', 'types', 'priorities', 'statuses'));
    }

    public function update(UpdateMaintenanceCaseRequest $request, MaintenanceCase $maintenanceCase)
    {
        $this->caseService->update(
            $maintenanceCase,
            UpdateMaintenanceCaseDTO::fromArray($request->validated())
        );

        return redirect()->route('maintenance-cases.show', $maintenanceCase)
            ->with('success', 'Caso actualizado correctamente.');
    }

    public function close(CloseMaintenanceCaseRequest $request, MaintenanceCase $maintenanceCase)
    {
        $this->caseService->close($maintenanceCase, $request->validated());

        return redirect()->route('maintenance-cases.show', $maintenanceCase)
            ->with('success', 'Caso cerrado correctamente.');
    }

    public function addItem(StoreMaintenanceItemRequest $request, MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.edit');

        $dto = CreateMaintenanceItemDTO::fromArray(array_merge(
            $request->validated(),
            ['maintenance_case_id' => $maintenanceCase->id]
        ));

        $this->caseService->addItem($maintenanceCase, $dto);

        return back()->with('success', 'Item agregado correctamente.');
    }

    public function removeItem(MaintenanceCase $maintenanceCase, int $item)
    {
        $this->authorize('maintenance-case.edit');

        $maintenanceItem = $maintenanceCase->items()->findOrFail($item);
        $this->caseService->removeItem($maintenanceCase, $maintenanceItem);

        return back()->with('success', 'Item eliminado correctamente.');
    }

    public function destroy(MaintenanceCase $maintenanceCase)
    {
        $this->authorize('maintenance-case.delete');

        $this->caseService->delete($maintenanceCase);

        return redirect()->route('maintenance-cases.index')
            ->with('success', 'Caso eliminado correctamente.');
    }
}
