<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('maintenance_type', 30)->nullable()->index();
            $table->string('asset_type', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('problem_description')->nullable();
            $table->text('diagnosis_template')->nullable();
            $table->text('actions_template')->nullable();
            $table->json('steps_json')->nullable();
            $table->unsignedSmallInteger('next_maintenance_interval_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_templates');
    }
};
