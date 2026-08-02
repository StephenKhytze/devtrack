<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeviceStatus;
use App\Models\Room;
use App\Models\Device;
use App\Models\DevicePart;
use App\Models\MaintenanceLog;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // Device Statuses
        // ============================================================

        $good        = DeviceStatus::create(['label' => 'Good',           'color' => 'green']);
        $maintenance = DeviceStatus::create(['label' => 'Maintenance',    'color' => 'orange']);
        $oos         = DeviceStatus::create(['label' => 'Out of Service', 'color' => 'red']);

        // ============================================================
        // Admin account
        // ============================================================

        $admin_user = User::create([
            'username'    => 'admin',
            'password'    => bcrypt('admin123'),
            'access_type' => 'admin',
        ]);

        $staff1 = User::create([
            'username'    => 'jdelacruz',
            'password'    => bcrypt('password'),
            'access_type' => 'staff',
        ]);

        $staff2 = User::create([
            'username'    => 'mreyes',
            'password'    => bcrypt('password'),
            'access_type' => 'staff',
        ]);

        // Rooms, devices, parts, logs — each in its own seeder class
        $this->call([
            RoomSeeder::class,
            InventorySeeder::class,
            MaintenanceLogSeeder::class,
        ]);
    }
}
