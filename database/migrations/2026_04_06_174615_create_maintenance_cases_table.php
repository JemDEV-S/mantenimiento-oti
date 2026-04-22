<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_cases', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('maintenance_campaigns')->nullOnDelete();
            $table->unsignedBigInteger('reported_by_employee_id')->nullable(); // id de empleado que reporta
            $table->unsignedBigInteger('assigned_technician_id')->nullable();
            $table->string('maintenance_type', 30);  // preventivo, correctivo, diagnostico, emergencia
            $table->string('priority', 20)->default('media');
            $table->string('status', 30)->default('registrado');
            $table->text('problem_description')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('actions_taken')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->string('conformity_name')->nullable();
            $table->timestamp('conformity_date')->nullable();
            $table->decimal('total_cost', 12, 2)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('reported_by_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('assigned_technician_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['asset_id', 'status']);
            $table->index('campaign_id');
            $table->index('assigned_technician_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_cases');
    }
};
