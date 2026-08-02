<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_parts', function (Blueprint $table) {
            $table->string('inventory_number')->nullable()->after('model_num');
            $table->string('serial_number')->nullable()->after('inventory_number');
        });
    }

    public function down(): void
    {
        Schema::table('device_parts', function (Blueprint $table) {
            $table->dropColumn(['inventory_number', 'serial_number']);
        });
    }
};
