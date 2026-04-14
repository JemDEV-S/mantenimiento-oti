<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Models\Asset;
use App\Models\Document;
use App\Models\MaintenanceCase;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $generatedBy = User::where('email', 'superadmin@mdsj.local')->value('id')
            ?? User::where('email', 'carlos.ramirez@mdsj.local')->value('id');

        $asset = Asset::where('internal_code', 'EQ-TES-002')->first();
        $case = MaintenanceCase::where('code', 'MC-2026-002')->first();

        if ($asset) {
            Document::updateOrCreate(
                ['reference_type' => Asset::class, 'reference_id' => $asset->id, 'code' => 'DOC-INV-001'],
                ['document_type' => DocumentType::INVENTARIO, 'title' => 'Ficha tecnica del activo EQ-TES-002', 'file_path' => 'demo/inventarios/EQ-TES-002.pdf', 'generated_by' => $generatedBy, 'generated_at' => now()->subDays(15), 'meta_json' => ['source' => 'seeder']]
            );
        }

        if ($case) {
            Document::updateOrCreate(
                ['reference_type' => MaintenanceCase::class, 'reference_id' => $case->id, 'code' => 'DOC-MNT-002'],
                ['document_type' => DocumentType::INFORME_TECNICO, 'title' => 'Informe tecnico del caso MC-2026-002', 'file_path' => 'demo/mantenimiento/MC-2026-002-informe.pdf', 'generated_by' => $generatedBy, 'generated_at' => now()->subDays(9), 'meta_json' => ['source' => 'seeder']]
            );
        }
    }
}
