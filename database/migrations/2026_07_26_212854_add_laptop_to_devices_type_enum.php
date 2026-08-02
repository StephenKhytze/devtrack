<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * NOTE: This assumes the devices.type enum currently contains:
     * desktop, printer, photocopier, telephone, aircon, appliance, network, monitor, other
     *
     * If your actual enum list differs, update the ENUM(...) list below
     * to match your real column definition before running this migration.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE devices MODIFY COLUMN type ENUM(
            'desktop', 'printer', 'photocopier', 'telephone', 'aircon',
            'appliance', 'network', 'monitor', 'laptop', 'other'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Reverting will fail if any existing rows have type = 'laptop'.
        // Update those rows to another type before rolling back.
        DB::statement("ALTER TABLE devices MODIFY COLUMN type ENUM(
            'desktop', 'printer', 'photocopier', 'telephone', 'aircon',
            'appliance', 'network', 'monitor', 'other'
        ) NOT NULL");
    }
};
