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
        Schema::create('agent_devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('hostname')->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->string('device_model')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('agent_version', 30)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->string('status', 20)->default('activo');
            $table->jsonb('last_snapshot_json')->nullable();
            $table->string('api_token', 80)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_devices');
    }
};
