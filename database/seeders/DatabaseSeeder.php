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

        $good        = DeviceStatus::firstOrCreate(['label' => 'Good'],           ['color' => 'green']);
        $maintenance = DeviceStatus::firstOrCreate(['label' => 'Maintenance'],    ['color' => 'orange']);
        $oos         = DeviceStatus::firstOrCreate(['label' => 'Out of Service'], ['color' => 'red']);

        // ============================================================
        // Admin account
        // ============================================================

        $admin_user = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password'    => bcrypt('admin123'),
                'access_type' => 'admin',
            ]
        );

        $staff1 = User::firstOrCreate(
            ['username' => 'jdelacruz'],
            [
                'password'    => bcrypt('password'),
                'access_type' => 'staff',
            ]
        );

        $staff2 = User::firstOrCreate(
            ['username' => 'mreyes'],
            [
                'password'    => bcrypt('password'),
                'access_type' => 'staff',
            ]
        );

        // Rooms, storage rooms, devices, parts, logs — each in its own seeder class
        $this->call([
            RoomSeeder::class,
            StorageRoomSeeder::class,
            InventorySeeder::class,
        ]);
    }
}
