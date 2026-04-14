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
        Schema::create('campaign_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('maintenance_campaigns')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('assigned_technician_id')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('attended_date')->nullable();
            $table->string('status', 30)->default('pendiente');
            $table->unsignedBigInteger('maintenance_case_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('assigned_technician_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('maintenance_case_id')->references('id')->on('maintenance_cases')->nullOnDelete();

            $table->unique(['campaign_id', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_assets');
    }
};
