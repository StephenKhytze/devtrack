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
        // Rooms
        // ============================================================

        $frontDesk   = Room::create(['name' => 'Front Desk',          'pos_x' => 29.366, 'pos_y' => 84.648, 'width' => 43.66,  'height' => 13.035, 'image' => null]);
        $contributions = Room::create(['name' => 'Contributions',     'pos_x' => 29.296, 'pos_y' => 66.829, 'width' => 43.659, 'height' => 13.037, 'image' => null]);
        $cashier     = Room::create(['name' => 'Cashier',             'pos_x' => 0.986,  'pos_y' => 51.92,  'width' => 19.577, 'height' => 28.879, 'image' => null]);
        $lhioHead    = Room::create(['name' => 'LHIO Head',           'pos_x' => 80.07,  'pos_y' => 58.16, 'width' => 18.803, 'height' => 39.6,   'image' => null]);
        $wellness    = Room::create(['name' => 'Wellness Area',       'pos_x' => 79.89,  'pos_y' => 2.19,   'width' => 12.87,  'height' => 36.41,  'image' => 'wellness.png']);
        $membership  = Room::create(['name' => 'Membership',          'pos_x' => 61.408, 'pos_y' => 2.151,  'width' => 10.915, 'height' => 33.794, 'image' => 'membership.png']);
        $admin       = Room::create(['name' => 'Claims and Admin',    'pos_x' => 48.873, 'pos_y' => 2.151,  'width' => 10.915, 'height' => 33.794, 'image' => 'claims-admin.png']);
        $itRoom      = Room::create(['name' => 'Server Room',         'pos_x' => 31.197, 'pos_y' => 1.997,  'width' => 11.197, 'height' => 13.671, 'image' => null]);
        $alcala      = Room::create(['name' => "Ma'am Alcala's Desk", 'pos_x' => 31.196, 'pos_y' => 18.278, 'width' => 6.408,  'height' => 15.82,  'image' => 'alcala.png']);

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

        // ============================================================
        // Devices — Claims and Admin
        // ============================================================

        $adminDesktop1 = Device::create([
            'name'      => 'Claims Desktop 1',
            'type'      => 'desktop',
            'model_num' => 'Dell OptiPlex 7090',
            'specs'     => 'Intel i5, 8GB RAM, 256GB SSD',
            'sub_parts' => true,
            'status_id' => $good->id,
            'room_id'   => $admin->id,
            'pos_x'     => 30.00,
            'pos_y'     => 25.00,
        ]);

        DevicePart::create(['device_id' => $adminDesktop1->id, 'name' => 'Monitor',  'model_num' => 'Dell P2422H',      'specs' => '24 inch FHD IPS',    'status_id' => $good->id]);
        DevicePart::create(['device_id' => $adminDesktop1->id, 'name' => 'Keyboard', 'model_num' => 'Dell KB216',       'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $adminDesktop1->id, 'name' => 'Mouse',    'model_num' => 'Dell MS116',       'specs' => 'USB Optical',        'status_id' => $good->id]);

        $adminDesktop2 = Device::create([
            'name'      => 'Claims Desktop 2',
            'type'      => 'desktop',
            'model_num' => 'Dell OptiPlex 7090',
            'specs'     => 'Intel i5, 8GB RAM, 256GB SSD',
            'sub_parts' => true,
            'status_id' => $maintenance->id,
            'room_id'   => $admin->id,
            'pos_x'     => 60.00,
            'pos_y'     => 25.00,
        ]);

        DevicePart::create(['device_id' => $adminDesktop2->id, 'name' => 'Monitor',  'model_num' => 'Dell P2422H',      'specs' => '24 inch FHD IPS',    'status_id' => $maintenance->id]);
        DevicePart::create(['device_id' => $adminDesktop2->id, 'name' => 'Keyboard', 'model_num' => 'Dell KB216',       'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $adminDesktop2->id, 'name' => 'Mouse',    'model_num' => 'Dell MS116',       'specs' => 'USB Optical',        'status_id' => $good->id]);

        $adminPrinter = Device::create([
            'name'      => 'Claims Printer',
            'type'      => 'printer',
            'model_num' => 'HP LaserJet Pro M404n',
            'specs'     => 'Monochrome, 38 ppm, USB/LAN',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $admin->id,
            'pos_x'     => 45.00,
            'pos_y'     => 70.00,
        ]);

        $adminDesktop3 = Device::create([
            'name'      => 'Admin Desktop',
            'type'      => 'desktop',
            'model_num' => 'HP EliteDesk 800 G6',
            'specs'     => 'Intel i7, 16GB RAM, 512GB SSD',
            'sub_parts' => true,
            'status_id' => $good->id,
            'room_id'   => $admin->id,
            'pos_x'     => 30.00,
            'pos_y'     => 65.00,
        ]);

        DevicePart::create(['device_id' => $adminDesktop3->id, 'name' => 'Monitor',  'model_num' => 'HP V24i G5',       'specs' => '24 inch FHD',        'status_id' => $good->id]);
        DevicePart::create(['device_id' => $adminDesktop3->id, 'name' => 'Keyboard', 'model_num' => 'HP USB Keyboard',  'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $adminDesktop3->id, 'name' => 'Mouse',    'model_num' => 'HP USB Mouse',     'specs' => 'USB Optical',        'status_id' => $good->id]);

        // ============================================================
        // Devices — Membership
        // ============================================================

        $memberDesktop1 = Device::create([
            'name'      => 'Membership Desktop 1',
            'type'      => 'desktop',
            'model_num' => 'Lenovo ThinkCentre M70s',
            'specs'     => 'Intel i5, 8GB RAM, 256GB SSD',
            'sub_parts' => true,
            'status_id' => $good->id,
            'room_id'   => $membership->id,
            'pos_x'     => 25.00,
            'pos_y'     => 30.00,
        ]);

        DevicePart::create(['device_id' => $memberDesktop1->id, 'name' => 'Monitor',  'model_num' => 'Lenovo L24i-30',  'specs' => '24 inch FHD',        'status_id' => $good->id]);
        DevicePart::create(['device_id' => $memberDesktop1->id, 'name' => 'Keyboard', 'model_num' => 'Lenovo USB KB',   'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $memberDesktop1->id, 'name' => 'Mouse',    'model_num' => 'Lenovo USB Mouse','specs' => 'USB Optical',        'status_id' => $good->id]);

        $memberDesktop2 = Device::create([
            'name'      => 'Membership Desktop 2',
            'type'      => 'desktop',
            'model_num' => 'Lenovo ThinkCentre M70s',
            'specs'     => 'Intel i5, 8GB RAM, 256GB SSD',
            'sub_parts' => true,
            'status_id' => $oos->id,
            'room_id'   => $membership->id,
            'pos_x'     => 65.00,
            'pos_y'     => 30.00,
        ]);

        DevicePart::create(['device_id' => $memberDesktop2->id, 'name' => 'Monitor',  'model_num' => 'Lenovo L24i-30',  'specs' => '24 inch FHD',        'status_id' => $oos->id]);
        DevicePart::create(['device_id' => $memberDesktop2->id, 'name' => 'Keyboard', 'model_num' => 'Lenovo USB KB',   'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $memberDesktop2->id, 'name' => 'Mouse',    'model_num' => 'Lenovo USB Mouse','specs' => 'USB Optical',        'status_id' => $good->id]);

        $memberPrinter = Device::create([
            'name'      => 'Membership Printer',
            'type'      => 'printer',
            'model_num' => 'Canon imageCLASS LBP6030',
            'specs'     => 'Monochrome Laser, 18 ppm, USB',
            'sub_parts' => false,
            'status_id' => $maintenance->id,
            'room_id'   => $membership->id,
            'pos_x'     => 45.00,
            'pos_y'     => 70.00,
        ]);

        $memberPhone = Device::create([
            'name'      => 'Membership Telephone',
            'type'      => 'telephone',
            'model_num' => 'Panasonic KX-TS500',
            'specs'     => 'Corded, Single Line',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $membership->id,
            'pos_x'     => 80.00,
            'pos_y'     => 70.00,
        ]);

        // ============================================================
        // Devices — Wellness and Supply Room
        // ============================================================

        $wellnessDesktop = Device::create([
            'name'      => 'Wellness Desktop',
            'type'      => 'desktop',
            'model_num' => 'Acer Veriton M4680G',
            'specs'     => 'Intel i3, 4GB RAM, 1TB HDD',
            'sub_parts' => true,
            'status_id' => $good->id,
            'room_id'   => $wellness->id,
            'pos_x'     => 25.00,
            'pos_y'     => 35.00,
        ]);

        DevicePart::create(['device_id' => $wellnessDesktop->id, 'name' => 'Monitor',  'model_num' => 'Acer V246HQL',    'specs' => '24 inch FHD',        'status_id' => $good->id]);
        DevicePart::create(['device_id' => $wellnessDesktop->id, 'name' => 'Keyboard', 'model_num' => 'Acer USB KB',     'specs' => 'USB Wired',          'status_id' => $good->id]);
        DevicePart::create(['device_id' => $wellnessDesktop->id, 'name' => 'Mouse',    'model_num' => 'Acer USB Mouse',  'specs' => 'USB Optical',        'status_id' => $good->id]);

        $wellnessPrinter = Device::create([
            'name'      => 'Wellness Printer',
            'type'      => 'printer',
            'model_num' => 'Epson L3210',
            'specs'     => 'Color Inkjet, Print/Scan/Copy, USB',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $wellness->id,
            'pos_x'     => 65.00,
            'pos_y'     => 35.00,
        ]);

        $wellnessAircon = Device::create([
            'name'      => 'Wellness Aircon',
            'type'      => 'aircon',
            'model_num' => 'Carrier 1.5HP Inverter',
            'specs'     => '1.5 HP, Inverter, Split Type',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $wellness->id,
            'pos_x'     => 50.00,
            'pos_y'     => 10.00,
        ]);

        $wellnessPhone = Device::create([
            'name'      => 'Wellness Telephone',
            'type'      => 'telephone',
            'model_num' => 'Panasonic KX-TS500',
            'specs'     => 'Corded, Single Line',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $wellness->id,
            'pos_x'     => 80.00,
            'pos_y'     => 70.00,
        ]);

        // ============================================================
        // Devices — Ma'am Alcala's Desk
        // ============================================================

        $alcalaDesktop = Device::create([
            'name'      => "Alcala's Desktop",
            'type'      => 'desktop',
            'model_num' => 'HP EliteDesk 800 G6',
            'specs'     => 'Intel i7, 16GB RAM, 512GB SSD',
            'sub_parts' => true,
            'status_id' => $good->id,
            'room_id'   => $alcala->id,
            'pos_x'     => 40.00,
            'pos_y'     => 40.00,
        ]);

        DevicePart::create(['device_id' => $alcalaDesktop->id, 'name' => 'Monitor',  'model_num' => 'HP EliteDisplay E243', 'specs' => '24 inch FHD IPS', 'status_id' => $good->id]);
        DevicePart::create(['device_id' => $alcalaDesktop->id, 'name' => 'Keyboard', 'model_num' => 'HP USB Keyboard',      'specs' => 'USB Wired',       'status_id' => $good->id]);
        DevicePart::create(['device_id' => $alcalaDesktop->id, 'name' => 'Mouse',    'model_num' => 'HP USB Mouse',         'specs' => 'USB Optical',     'status_id' => $good->id]);

        $alcalaPhone = Device::create([
            'name'      => "Alcala's Telephone",
            'type'      => 'telephone',
            'model_num' => 'Panasonic KX-TS500',
            'specs'     => 'Corded, Single Line',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => $alcala->id,
            'pos_x'     => 75.00,
            'pos_y'     => 40.00,
        ]);

        $alcalaPrinter = Device::create([
            'name'      => "Alcala's Printer",
            'type'      => 'printer',
            'model_num' => 'HP LaserJet Pro M15w',
            'specs'     => 'Monochrome, 19 ppm, USB/WiFi',
            'sub_parts' => false,
            'status_id' => $maintenance->id,
            'room_id'   => $alcala->id,
            'pos_x'     => 75.00,
            'pos_y'     => 70.00,
        ]);

        // ============================================================
        // Standalone devices
        // ============================================================

        Device::create([
            'name'      => 'Lobby Aircon 1',
            'type'      => 'aircon',
            'model_num' => 'Carrier 2.0HP Inverter',
            'specs'     => '2.0 HP, Inverter, Split Type',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => null,
            'pos_x'     => 15.00,
            'pos_y'     => 5.00,
        ]);

        Device::create([
            'name'      => 'Lobby Aircon 2',
            'type'      => 'aircon',
            'model_num' => 'Carrier 2.0HP Inverter',
            'specs'     => '2.0 HP, Inverter, Split Type',
            'sub_parts' => false,
            'status_id' => $oos->id,
            'room_id'   => null,
            'pos_x'     => 50.00,
            'pos_y'     => 5.00,
        ]);

        Device::create([
            'name'      => 'Network Switch',
            'type'      => 'network',
            'model_num' => 'Cisco SG110-16',
            'specs'     => '16-Port Gigabit Switch',
            'sub_parts' => false,
            'status_id' => $good->id,
            'room_id'   => null,
            'pos_x'     => 35.00,
            'pos_y'     => 5.00,
        ]);

        // ============================================================
        // Maintenance Logs
        // ============================================================

        MaintenanceLog::create([
            'device_id'        => $adminDesktop2->id,
            'performed_by'     => $staff1->id,
            'date'             => '2026-01-10',
            'description'      => 'Monitor displaying flickering lines. Checked cable connections and reseated display port cable. Issue persists — monitor flagged for replacement.',
            'status_before_id' => $good->id,
            'status_after_id'  => $maintenance->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $memberDesktop2->id,
            'performed_by'     => $staff1->id,
            'date'             => '2026-01-15',
            'description'      => 'Unit failed to power on. Checked power supply and motherboard. Determined power supply unit is faulty. Unit pulled from service pending replacement parts.',
            'status_before_id' => $good->id,
            'status_after_id'  => $oos->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $memberPrinter->id,
            'performed_by'     => $staff2->id,
            'date'             => '2026-02-03',
            'description'      => 'Printer producing faded output. Cleaned drum unit and replaced toner cartridge. Print quality improved but still slightly below standard — scheduled for follow-up check.',
            'status_before_id' => $good->id,
            'status_after_id'  => $maintenance->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $alcalaPrinter->id,
            'performed_by'     => $staff2->id,
            'date'             => '2026-02-20',
            'description'      => 'Paper jam occurring frequently. Cleaned paper feed rollers and removed debris from paper path. Issue reduced but not fully resolved — monitoring.',
            'status_before_id' => $good->id,
            'status_after_id'  => $maintenance->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $adminDesktop1->id,
            'performed_by'     => $admin_user->id,
            'date'             => '2026-03-05',
            'description'      => 'Routine maintenance performed. Cleaned internals, updated drivers and Windows, ran disk cleanup and defragmentation. Unit returned to normal operation.',
            'status_before_id' => $good->id,
            'status_after_id'  => $good->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $wellnessAircon->id,
            'performed_by'     => $staff1->id,
            'date'             => '2026-03-12',
            'description'      => 'Annual aircon maintenance. Cleaned filters, checked refrigerant levels, inspected condenser coils. Unit operating within normal parameters.',
            'status_before_id' => $good->id,
            'status_after_id'  => $good->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $adminDesktop3->id,
            'performed_by'     => $staff2->id,
            'date'             => '2026-03-18',
            'description'      => 'System running slow. Upgraded RAM from 8GB to 16GB and replaced HDD with SSD. System performance significantly improved.',
            'status_before_id' => $maintenance->id,
            'status_after_id'  => $good->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $wellnessPrinter->id,
            'performed_by'     => $staff1->id,
            'date'             => '2026-04-02',
            'description'      => 'Ink levels depleted. Replaced all four ink cartridges (CMYK). Ran test print — output quality confirmed good.',
            'status_before_id' => $maintenance->id,
            'status_after_id'  => $good->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $memberDesktop1->id,
            'performed_by'     => $admin_user->id,
            'date'             => '2026-04-15',
            'description'      => 'Routine checkup. Cleaned dust from internals, updated antivirus definitions, checked all cable connections. No issues found.',
            'status_before_id' => $good->id,
            'status_after_id'  => $good->id,
        ]);

        MaintenanceLog::create([
            'device_id'        => $alcalaDesktop->id,
            'performed_by'     => $staff2->id,
            'date'             => '2026-05-08',
            'description'      => 'User reported system freezing intermittently. Ran memory diagnostic — no errors found. Cleaned thermal paste on CPU and reseated RAM sticks. System stable after restart.',
            'status_before_id' => $maintenance->id,
            'status_after_id'  => $good->id,
        ]);
    }
}
