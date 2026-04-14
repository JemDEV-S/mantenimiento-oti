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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('movement_type', 30);        // Enum AssetMovementType->asignacion, devolucion, transferencia, baja
            $table->unsignedBigInteger('origin_unit_id')->nullable();
            $table->unsignedBigInteger('destination_unit_id')->nullable();
            $table->unsignedBigInteger('from_employee_id')->nullable();
            $table->unsignedBigInteger('to_employee_id')->nullable();
            $table->date('movement_date');
            $table->string('reason')->nullable();
            $table->string('document_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('origin_unit_id')->references('id')->on('organizational_units')->nullOnDelete();
            $table->foreign('destination_unit_id')->references('id')->on('organizational_units')->nullOnDelete();
            $table->foreign('from_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('to_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
