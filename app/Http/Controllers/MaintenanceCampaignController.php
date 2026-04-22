<?php

namespace App\Http\Controllers;

use App\DTOs\MaintenanceCampaign\CreateMaintenanceCampaignDTO;
use App\DTOs\MaintenanceCampaign\UpdateMaintenanceCampaignDTO;
use App\Enums\CampaignStatus;
use App\Http\Requests\MaintenanceCampaign\StoreMaintenanceCampaignRequest;
use App\Http\Requests\MaintenanceCampaign\UpdateMaintenanceCampaignRequest;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\MaintenanceCampaign;
use App\Models\OrganizationalUnit;
use App\Services\Document\DocumentGeneratorService;
use App\Services\MaintenanceCampaign\MaintenanceCampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceCampaignController extends Controller
{
    public function __construct(
        private readonly MaintenanceCampaignService $campaignService,
        private readonly DocumentGeneratorService $docGenerator,
    ) {}

    public function index()
    {
        $this->authorize('campaign.view');

        $campaigns = $this->campaignService->getPaginated(request()->only(['search', 'status']));
        $statuses  = CampaignStatus::cases();

        return view('campaigns.index', compact('campaigns', 'statuses'));
    }

    public function create()
    {
        $this->authorize('campaign.create');

        $coordinators = Employee::where('is_active', true)->orderBy('full_name')->get();
        $statuses     = CampaignStatus::cases();

        return view('campaigns.create', compact('coordinators', 'statuses'));
    }

    public function store(StoreMaintenanceCampaignRequest $request)
    {
        $data = array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]);

        $campaign = $this->campaignService->create(
            CreateMaintenanceCampaignDTO::fromArray($data)
        );

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaña de mantenimiento creada correctamente.');
    }

    public function show(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.view');

        $campaign->load('coordinator', 'campaignAssets.asset', 'campaignAssets.assignedTechnician', 'maintenanceCases');

        $availableAssets       = Asset::whereNotIn('id', $campaign->campaignAssets()->pluck('asset_id'))->orderBy('name')->get();
        $technicians           = Employee::technicians()->orderBy('full_name')->get();
        $organizationalUnits   = OrganizationalUnit::where('is_active', true)->orderBy('name')->get();
        $maintenanceTypes      = MaintenanceType::cases();
        $priorities            = MaintenancePriority::cases();

        return view('campaigns.show', compact('campaign', 'availableAssets', 'technicians', 'organizationalUnits', 'maintenanceTypes', 'priorities'));
    }

    public function edit(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.edit');

        $campaign->load('campaignAssets.asset', 'campaignAssets.assignedTechnician');

        $coordinators        = Employee::where('is_active', true)->orderBy('full_name')->get();
        $statuses            = CampaignStatus::cases();
        $technicians         = Employee::technicians()->orderBy('full_name')->get();
        $organizationalUnits = OrganizationalUnit::where('is_active', true)->orderBy('name')->get();
        $maintenanceTypes    = MaintenanceType::cases();
        $priorities          = MaintenancePriority::cases();
        $availableAssets     = Asset::whereNotIn('id', $campaign->campaignAssets()->pluck('asset_id'))->orderBy('name')->get();

        return view('campaigns.edit', compact('campaign', 'coordinators', 'statuses', 'technicians', 'organizationalUnits', 'maintenanceTypes', 'priorities', 'availableAssets'));
    }

    public function update(UpdateMaintenanceCampaignRequest $request, MaintenanceCampaign $campaign)
    {
        $this->campaignService->update(
            $campaign,
            UpdateMaintenanceCampaignDTO::fromArray($request->validated())
        );

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.delete');

        $this->campaignService->delete($campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña eliminada correctamente.');
    }

    public function addAsset(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.edit');

        $this->campaignService->addAsset(
            $campaign,
            (int) request('asset_id'),
            request()->only(['assigned_technician_id', 'scheduled_date', 'notes'])
        );

        return back()->with('success', 'Activo agregado a la campaña.');
    }

    public function removeAsset(MaintenanceCampaign $campaign, int $assetId)
    {
        $this->authorize('campaign.edit');

        $this->campaignService->removeAsset($campaign, $assetId);

        return back()->with('success', 'Activo removido de la campaña.');
    }

    public function bulkAddByUnit(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.edit');

        $count = $this->campaignService->bulkAddByUnit(
            $campaign,
            (int) request('unit_id'),
            request()->only(['assigned_technician_id', 'scheduled_date', 'notes'])
        );

        return back()->with('success', "{$count} activo(s) agregado(s) a la campaña desde la unidad seleccionada.");
    }

    public function generateActa(Request $request, MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.view');

        $request->validate(['unit_id' => 'required|integer|exists:organizational_units,id']);

        $unit = OrganizationalUnit::findOrFail($request->unit_id);

        $document = $this->docGenerator->generateActaMantenimiento($campaign, $unit, auth()->user());

        $filename = str_replace(['/', '\\', ':'], '-', $document->title) . '.pdf';

        return Storage::disk('local')->download($document->file_path, $filename);
    }

    public function bulkCreateCases(MaintenanceCampaign $campaign)
    {
        $this->authorize('campaign.edit');

        $count = $this->campaignService->bulkCreateCases(
            $campaign,
            array_merge(
                request()->only(['assigned_technician_id', 'maintenance_type', 'priority', 'problem_description']),
                ['created_by' => auth()->id()]
            )
        );

        return back()->with('success', "{$count} caso(s) de mantenimiento creado(s) correctamente.");
    }
}
