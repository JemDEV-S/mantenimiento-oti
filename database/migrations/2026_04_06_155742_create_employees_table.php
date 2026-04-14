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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('position', 100)->nullable();
            $table->unsignedBigInteger('organizational_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_technician')->default(false);
            $table->string('specialty', 100)->nullable();
            $table->string('level', 30)->nullable();  // junior, mid, senior
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('organizational_unit_id');
            $table->index('is_technician');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index('employee_id');
        });

        Schema::table('organizational_units', function (Blueprint $table) {
            $table->unsignedBigInteger('responsible_employee_id')->nullable()->after('name')->constrained('employees')->nullOnDelete();
            $table->index('responsible_employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
