<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('date');
        });
    }

    public function down()
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
};
