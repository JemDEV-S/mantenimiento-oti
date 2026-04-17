<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_devices', function (Blueprint $table) {
            $table->jsonb('last_health_json')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agent_devices', function (Blueprint $table) {
            $table->dropColumn('last_health_json');
        });
    }
};
