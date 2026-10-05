<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Device;
use App\Models\DevicePart;
use App\Models\DeviceUpdateLog;
use App\Models\MaintenanceLog;
use App\Models\Room;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Replaces existing database data with realistic, professional mock data
     * while preserving all database schemas, table structures, room layouts,
     * sub-part associations, coordinates, and foreign key relationships.
     */
    public function up(): void
    {
        $dbName = config('database.connections.mysql.database', env('DB_DATABASE', 'devtrack'));
        $isMockDb = str_contains($dbName, 'demo') || str_contains($dbName, 'mock') || env('APP_USE_MOCK_DATA', false);

        // Safeguard: Only perform data masking/mocking if targeting demo/mock database or explicit mock flag
        if (!$isMockDb) {
            return;
        }

        DB::transaction(function () {
            $this->mockUsers();
            $deviceNameMap = $this->mockDevices();
            $this->mockDeviceParts();
            $this->mockMaintenanceLogs();
            $this->mockDeviceUpdateLogs($deviceNameMap);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Data anonymization and mock data replacements cannot be reverted to original private data.
     */
    public function down(): void
    {
        // Data migration cannot be reversed to restore original sensitive data.
    }

    /**
     * Sanitize and mock user accounts while preserving IDs and roles.
     */
    private function mockUsers(): void
    {
        $fictionalUsernames = [
            'alex.cruz',
            'maria.santos',
            'david.reyes',
            'elena.ramos',
            'marcus.rivera',
            'sophia.tan',
            'gabriel.torres',
            'camille.soriano',
            'rafael.aquino',
            'patricia.villanueva',
            'benjamin.flores',
            'isabella.navarro',
            'lucas.delrosario',
            'jasmine.dizon',
            'emilio.ocampo',
            'rowena.pascual',
            'ricardo.valenzuela',
            'katrina.salazar',
            'jonathan.corpuz',
            'kristine.lopez',
        ];

        $users = User::orderBy('id')->get();
        $staffIndex = 0;

        foreach ($users as $user) {
            if ($user->username === 'admin' || $user->id === 1) {
                $user->username = 'admin';
                $user->password = Hash::make('admin123');
                $user->access_type = 'admin';
            } else {
                $username = $fictionalUsernames[$staffIndex % count($fictionalUsernames)];
                if ($staffIndex >= count($fictionalUsernames)) {
                    $username .= ($staffIndex + 1);
                }
                $user->username = $username;
                $user->password = Hash::make('Password123!');
                $staffIndex++;
            }
            $user->save();
        }
    }

    /**
     * Replace device data with realistic mock data, preserving room assignments,
     * positions (pos_x, pos_y), types, sub_parts flags, and status IDs.
     *
     * @return array<int, string> Map of device ID => new device name
     */
    private function mockDevices(): array
    {
        $fictionalPersonnel = [
            'Alexander M. Cruz',
            'Maria Clara Santos',
            'Gabriel R. Ramos',
            'Elena V. Morales',
            'David L. Reyes',
            'Angela Mae Bautista',
            'Danilo P. Mendoza',
            'Sophia Marie Hernandez',
            'Rafael C. Aquino',
            'Patricia Ann Villanueva',
            'Benjamin T. Flores',
            'Isabella Rose Navarro',
            'Lucas Daniel Del Rosario',
            'Camille Joyce Soriano',
            'Christian Paul Garcia',
            'Jasmine Nicole Dizon',
            'Emilio S. Ocampo',
            'Rowena C. Pascual',
            'Ricardo J. Valenzuela',
            'Katrina Marie Salazar',
            'Jonathan E. Corpuz',
            'Kristine Mae Lopez',
            'Mark Anthony Castillo',
            'Diana Rose Santiago',
            'Eduardo G. Mangahas',
            'Theresa Bea Lim',
            'Francis Xavier Tolentino',
            'Janice Marie Domingo',
            'Jericho Keith Gutierrez',
            'Maricel F. Bernales',
            'Rene Albert Mercado',
            'Giselle Anne Alcantara',
            'Nathaniel R. Macaraeg',
            'Sheryl Mae Pineda',
            'Dominic Jose Manalo',
            'Aileen Ruth Guinto',
            'Vicente Carlos Laurel',
            'Charmaine Joy De Vera',
            'Roderick V. Samonte',
            'Beatriz Marie Cojuangco',
            'Antonino S. Legaspi',
            'Clarissa Joy Buenaflor',
            'Ramoncito D. Carandang',
            'Rosalinda P. Magbanua',
            'Kenneth Dave Dimayuga',
            'Lorna May Evangelista',
            'Joaquin Miguel Malabanan',
            'Lourdes Cristina Panganiban',
            'Reginald Ian Quizon',
            'Monina Rose Villafuerte',
        ];

        $modelsByType = [
            'desktop' => [
                'ASUS ExpertCenter D5 SFF (D500SC)',
                'HP ProDesk 400 G7 SFF',
                'Dell OptiPlex 7090 Micro',
                'Lenovo ThinkCentre M70s Gen 3',
                'Acer Veriton X4680G',
                'Dell OptiPlex 5080 Tower',
                'HP EliteDesk 800 G6 Small Form Factor',
                'ASUS S5 SFF (S501SER)',
            ],
            'laptop' => [
                'Lenovo ThinkPad T14 Gen 3',
                'Dell Latitude 5430 14"',
                'HP ProBook 450 G9',
                'ASUS ExpertBook B1 (B1502)',
                'Lenovo ThinkPad L14 Gen 4',
                'Dell Latitude 3520',
            ],
            'printer' => [
                'HP LaserJet Pro MFP 4103fdw',
                'Epson EcoTank L3210 All-in-One',
                'Brother HL-L5200DW Enterprise Laser',
                'Canon imageCLASS MF445dw',
                'Epson LQ-310 24-Pin Dot Matrix',
                'HP Color LaserJet Pro M283fdw',
            ],
            'photocopier' => [
                'Canon imageRUNNER 2625i Multi-Function',
                'Kyocera TASKalfa 2554ci Heavy Duty',
                'Fuji Xerox DocuCentre-V 3065',
                'Ricoh MP 3055SP Multi-Function Copier',
            ],
            'telephone' => [
                'Cisco IP Phone 7841 Multi-Line',
                'Grandstream GRP2614 IP Phone',
                'Yealink SIP-T31P HD VoIP Phone',
                'Panasonic KX-TS880MX Integrated Corded Telephone',
            ],
            'aircon' => [
                'Carrier Aura Inverter 2.0HP Split Type',
                'Daikin FTKQ50TVM 2.0HP Inverter',
                'Panasonic Premium Inverter 2.5HP High Wall',
                'Koppel Super Inverter Floor Mounted 3.0HP',
                'Condura Prima Inverter 1.5HP Split Type',
            ],
            'appliance' => [
                'Sharp SJ-FTG18BVP 2-Door Inverter Refrigerator',
                'Panasonic NN-ST34HM Microwave Oven 25L',
                'Dowell WDL-520 Hot & Cold Water Dispenser',
                'Standard Industrial Electric Stand Fan 18"',
            ],
            'network' => [
                'Cisco Catalyst 2960-X 24-Port Gigabit Switch',
                'Ubiquiti UniFi Switch Pro 24 PoE',
                'MikroTik Cloud Router Switch CRS326-24G-2S+RM',
                'TP-Link TL-SG1024D 24-Port Gigabit Rackmount',
                'Fortinet FortiGate 60F Security Gateway',
            ],
            'monitor' => [
                'HP P24v G4 23.8" Full HD IPS Monitor',
                'Dell E2422H 24" FHD IPS Display',
                'ASUS VA24EHE 23.8" Eye Care IPS',
                'ViewSonic VA2432-H 24" IPS Frameless',
                'LG 24MP400-B 24" FHD IPS with FreeSync',
            ],
            'other' => [
                'APC Smart-UPS RT 3000VA 230V SURT3000XLI',
                'Fellowes Powershred 79Ci Cross-Cut Paper Shredder',
                'Zebra DS2208 1D/2D Corded Handheld Barcode Scanner',
                'Epson Perfection V39 II Color Flatbed Scanner',
            ],
        ];

        $specsByType = [
            'desktop' => [
                'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 16GB (2x8GB) DDR4 @3200MHz, Intel UHD Graphics 730',
                'Intel Core i5-12400 Processor 2.5GHz (18MB Cache, up to 4.4GHz, 6 cores, 12 threads), 512GB M.2 PCIe NVMe SSD, 16GB DDR4 @3200MHz, Gigabit LAN, Win 11 Pro',
                'Intel Core i5-10400 @2.90GHz (up to 4.30GHz, 6 cores), 8GB DDR4 RAM, 1TB SATA HDD + 256GB SSD, Intel UHD Graphics 630',
                'AMD Ryzen 5 Pro 5650G 3.9GHz (6 cores, 12 threads), 16GB DDR4 @3200MHz, 512GB NVMe SSD, Radeon Graphics, 300W 80+ Platinum PSU',
                'Intel Core i3-12100 Processor 3.3GHz (12MB Cache, 4 cores, 8 threads), 512GB M.2 NVMe SSD, 8GB DDR4 @3200MHz, Intel UHD Graphics 730',
            ],
            'laptop' => [
                'Intel Core i5-1235U (10 cores, 12 threads, up to 4.4GHz), 16GB DDR4 @3200MHz, 512GB M.2 PCIe NVMe SSD, 14.0" FHD (1920x1080) IPS, Wi-Fi 6, Windows 11 Pro',
                'Intel Core i7-1255U (10 cores, 12 threads, up to 4.7GHz), 16GB DDR4 @3200MHz, 1TB PCIe NVMe SSD, 15.6" FHD IPS Anti-Glare, Intel Iris Xe Graphics',
                'AMD Ryzen 5 5625U 2.3GHz (6 cores, 12 threads), 16GB DDR4 RAM, 512GB NVMe SSD, 14" IPS FHD 300 nits, Fingerprint Reader, 45Wh Battery',
            ],
            'printer' => [
                'Up to 40 ppm monochrome / color, Automatic 2-sided printing, 1200 x 1200 dpi, Gigabit Ethernet & Wi-Fi Direct, 250-sheet paper tray, Monthly duty cycle 80,000 pages',
                'Continuous Ink Tank System, High-yield 4500 pages black / 7500 pages color, 33 ppm draft, USB 2.0 & Wi-Fi Direct, Borderless 4R printing',
                '24-Pin Narrow Carriage Impact Dot Matrix Printer, 347 cps at 10 cpi, USB 2.0 & Bi-directional Parallel, 10,000 POH MTBF, 1 original + 4 copies',
            ],
            'photocopier' => [
                'Monochrome Laser A3 Multi-Function Printer, Print / Copy / Color Scan, 25 ppm (A4), 1200 x 1200 dpi, 7-inch Color Touch Panel, Duplex Automatic Document Feeder (DADF), 2x550 Sheet Cassettes',
                'A3 Color Multifunctional Copier, 25/25 ppm A4 color/mono, 1200 x 1200 dpi, 10.1 inch full-color touch panel, 4GB RAM, 32GB SSD + optional 320GB HDD, Dual Scan Document Processor',
            ],
            'telephone' => [
                '4-Line Gigabit IP Telephone, 3.2" 384x160-pixel graphical LCD with backlight, Dual-port Gigabit Ethernet, PoE enabled, Opus HD audio codec, RJ9 headset support',
                'Single-Line Corded Integrated Telephone, 16-Digit LCD with Clock, 30-Station One-Touch / Speed Dialer, Speakerphone, Ringer Indicator, Wall Mountable',
            ],
            'aircon' => [
                '2.0 HP Split Type Inverter Air Conditioner, Cooling Capacity 18,000 kJ/h, Inverter Smart Control, R32 Eco Refrigerant, Anti-Bacterial Filter, Gold Fin Condenser',
                '2.5 HP Wall Mounted Deluxe Inverter, Cooling Capacity 22,500 kJ/h, Nanoe-G Air Purification System, AEROWINGS airflow, 5-Star Energy Efficiency Rating',
                '3.0 HP Floor Standing Inverter Package Unit, 28,000 BTU/hr Cooling Capacity, 3D Airflow Auto Swing, Touch Control Panel & Wireless Remote, High Efficiency Compressor',
            ],
            'appliance' => [
                '230V / 60Hz, 8.5 cu.ft Two-Door Top Mount No-Frost Inverter Refrigerator, J-Tech Inverter Technology, Ag+ Nano Deodorizer, Tempered Glass Shelves',
                '25 Liters Solo Microwave Oven, 800W Microwave Power, 5 Power Levels, 288mm Turntable Glass, 9 Auto Cooking Menus, Child Safety Lock',
                'Bottom Load Water Dispenser, Hot, Cold & Normal Water Options, Child Safety Lock on Hot Water, Stainless Steel Water Tank, High Efficiency Compressor Cooling',
            ],
            'network' => [
                '24x 10/100/1000 Gigabit Ethernet Ports, 4x 1G SFP Uplinks, 370W PoE+ Power Budget, Stacking Support, LAN Base Feature Set, Layer 2 Switching Capacity 216 Gbps',
                '24x Gigabit RJ45 Ports, 2x 10G SFP+ Ports, Layer 3 Switching Features, UniFi Controller Cloud Management, 1.3" Touchscreen AR Switch Management',
                '24x Gigabit Ethernet Ports with PoE/PoE+ (380W total), 4x 10G SFP+ Cages, Dual Boot RouterOS/SwOS, Dual Power Supply Redundancy',
            ],
            'monitor' => [
                '23.8-inch Full HD (1920 x 1080) IPS Anti-Glare Display, 16:9, 75Hz Refresh Rate, 5ms Response Time, 250 cd/m², HDMI 1.4 & VGA, Tilt-Adjustable Stand, VESA Mount 100x100mm',
                '24.0-inch 1080p IPS Panel, 99% sRGB Color Gamut, Flicker-Free & Low Blue Light Technology, DisplayPort 1.2, HDMI 1.4, VGA, Integrated Cable Management',
            ],
            'other' => [
                '3000VA / 2700W Line-Interactive / On-Line UPS, 230V Input/Output, LCD Status Display, SmartSlot Interface, Pure Sine Wave Output on Battery',
                'Handheld 2D Area Imager Barcode Scanner, Point-and-Shoot Scanning of 1D/2D Barcodes, USB Interface Cable Included, IP42 Sealing, 5ft Drop Resistance',
            ],
        ];

        $devices = Device::with('room')->orderBy('id')->get();
        $deviceNameMap = [];

        $roomCounters = [];
        $typeCounters = [];
        $personnelIndex = 0;

        foreach ($devices as $device) {
            $type = $device->type ?: 'desktop';
            $roomName = $device->room?->name;
            $isStorage = (bool)($device->room?->is_storage ?? false);

            $roomKey = $roomName ?? 'Standalone';
            $roomCounters[$roomKey] = ($roomCounters[$roomKey] ?? 0) + 1;
            $typeCounters[$type] = ($typeCounters[$type] ?? 0) + 1;

            $roomCount = $roomCounters[$roomKey];
            $typeCount = $typeCounters[$type];

            // 1. Generate Contextual Mock Name
            $newName = $this->generateDeviceName(
                $device,
                $type,
                $roomName,
                $isStorage,
                $roomCount,
                $typeCount,
                $fictionalPersonnel,
                $personnelIndex
            );

            // 2. Select matching Model and Specs
            $modelList = $modelsByType[$type] ?? $modelsByType['desktop'];
            $specList  = $specsByType[$type] ?? $specsByType['desktop'];

            $newModel = $modelList[($device->id - 1) % count($modelList)];
            $newSpecs = $specList[($device->id - 1) % count($specList)];

            // 3. Realistic, unique Serial Number
            $typeCode = strtoupper(substr($type, 0, 3));
            $hashSuffix = strtoupper(substr(md5($device->id . '_dt_salt_serial'), 0, 4));
            $newSerial = sprintf('SN-%s-2026-%04d-%s', $typeCode, $device->id, $hashSuffix);

            // 4. Realistic, structured Inventory Number (preserves D5 standard format)
            $monthCode = sprintf('%02d', (($device->id % 12) + 1));
            $newInventory = sprintf('D5-2026-%s-%04d', $monthCode, $device->id);

            // Save updates while preserving room_id, pos_x, pos_y, status_id, sub_parts
            $device->name             = $newName;
            $device->model_num        = $newModel;
            $device->specs            = $newSpecs;
            $device->serial_number    = $newSerial;
            $device->inventory_number = $newInventory;
            $device->save();

            $deviceNameMap[$device->id] = $newName;
        }

        return $deviceNameMap;
    }

    /**
     * Helper to construct a realistic device name based on room context and equipment type.
     */
    private function generateDeviceName(
        Device $device,
        string $type,
        ?string $roomName,
        bool $isStorage,
        int $roomCount,
        int $typeCount,
        array $fictionalPersonnel,
        int &$personnelIndex
    ): string {
        // Storage items
        if ($isStorage) {
            $storageLabel = $roomName ? "({$roomName})" : '(Storage)';
            return match ($type) {
                'desktop'     => "Spare Workstation PC #{$roomCount} {$storageLabel}",
                'laptop'      => "Backup Service Laptop #{$roomCount} {$storageLabel}",
                'printer'     => "Surplus Office Printer #{$roomCount} {$storageLabel}",
                'photocopier' => "Archived Photocopier #{$roomCount} {$storageLabel}",
                'monitor'     => "Spare Monitor Unit #{$roomCount} {$storageLabel}",
                'telephone'   => "Reserve Telephone Handset #{$roomCount} {$storageLabel}",
                'network'     => "Reserve Network Switch #{$roomCount} {$storageLabel}",
                'aircon'      => "Surplus AC Unit #{$roomCount} {$storageLabel}",
                default       => "Archived Equipment #{$roomCount} {$storageLabel}",
            };
        }

        // Workstation devices (Desktops & Laptops)
        if ($type === 'desktop' || $type === 'laptop') {
            $person = $fictionalPersonnel[$personnelIndex % count($fictionalPersonnel)];
            $personnelIndex++;

            if ($roomName === 'Cashier') {
                return "{$person} (Cashier PC {$roomCount})";
            }
            if ($roomName === 'LHIO Head') {
                return "{$person} (LHIO Head PC)";
            }
            if ($roomName === 'Front Desk') {
                return "{$person} (Front Desk Terminal {$roomCount})";
            }
            if ($roomName === 'Server Room') {
                return match ($roomCount) {
                    1 => 'Primary Domain Controller & Gateway Server',
                    2 => 'Local Database & Backup Storage Server',
                    default => "Server Room Terminal #{$roomCount}",
                };
            }
            if ($roomName === 'Conference Room') {
                return "Conference Room Main Presentation Station";
            }
            if ($roomName === 'IT and Claims') {
                return ($type === 'laptop')
                    ? "{$person} (Claims Laptop)"
                    : "{$person} (IT & Claims Workstation)";
            }
            if ($roomName === 'Membership') {
                return "{$person} (Membership Section)";
            }
            if ($roomName === 'Contributions') {
                return "{$person} (Contributions Desk)";
            }
            if ($roomName === 'Admin') {
                return "{$person} (Administrative Section)";
            }
            if ($roomName === 'Wellness Area') {
                return "Wellness Center Kiosk Terminal #{$roomCount}";
            }
            if ($roomName === 'Pantry') {
                return "Staff Pantry Common Terminal";
            }

            if ($type === 'laptop') {
                return "{$person} (Laptop)";
            }

            return $roomName ? "{$person} ({$roomName})" : "{$person} (Workstation #{$typeCount})";
        }

        // Shared Office Equipment by Type
        return match ($type) {
            'printer' => match ($roomName) {
                'Cashier'       => "Dot Matrix Receipt Printer (Cashier Desk #{$roomCount})",
                'Front Desk'    => "Front Desk Heavy-Duty Network Printer",
                'IT and Claims' => "IT & Claims High-Yield Network Printer",
                'Membership'    => "Membership Department Multi-Function Printer",
                'Admin'         => "Administrative Laser Printer",
                default         => $roomName ? "{$roomName} Workgroup Printer #{$roomCount}" : "Office Network Printer #{$typeCount}",
            },
            'photocopier' => match ($roomName) {
                'Admin'         => "Central Office Multi-Function Photocopier",
                'IT and Claims' => "Claims Department High-Volume Copier/Scanner",
                default         => $roomName ? "{$roomName} Multi-Function Photocopier" : "Central Photocopier #{$typeCount}",
            },
            'telephone' => match ($roomName) {
                'Cashier'       => "Cashier Hotline IP Phone (Ext. 10{$roomCount})",
                'Front Desk'    => "Front Desk Inquiries Phone (Ext. 101)",
                'LHIO Head'     => "LHIO Head Executive IP Phone (Ext. 100)",
                'IT and Claims' => "IT Helpdesk Hotline Phone (Ext. 108)",
                default         => $roomName ? "{$roomName} Desk IP Phone (Ext. " . (110 + $typeCount) . ")" : "Office IP Phone (Ext. " . (110 + $typeCount) . ")",
            },
            'aircon' => match ($roomName) {
                'Server Room'     => "Server Room Inverter AC Unit #{$roomCount} (2.5HP)",
                'Conference Room' => "Conference Room Split AC #{$roomCount} (3.0HP)",
                'LHIO Head'       => "LHIO Head Executive Inverter AC (2.0HP)",
                default           => $roomName ? "{$roomName} Split-Type AC Unit #{$roomCount}" : "Floor AC Unit #{$typeCount}",
            },
            'appliance' => match ($roomName) {
                'Pantry'     => ($roomCount === 1) ? 'Staff Pantry Two-Door Inverter Refrigerator' : 'Staff Pantry Digital Microwave Oven 25L',
                'Front Desk' => 'Front Desk Commercial Water Dispenser (Hot/Cold)',
                default      => $roomName ? "{$roomName} Commercial Water Dispenser" : "Office Water Dispenser #{$typeCount}",
            },
            'network' => match ($roomName) {
                'Server Room' => "Core Distribution Switch 24-Port PoE+ (Rack A)",
                default       => $roomName ? "{$roomName} Edge Gigabit Switch #{$roomCount}" : "Network Access Switch #{$typeCount}",
            },
            'monitor' => match ($roomName) {
                'Server Room' => "Server Rack Monitoring Console 24\"",
                default       => $roomName ? "{$roomName} Secondary Display Monitor" : "Extended Display Monitor #{$typeCount}",
            },
            'other' => match ($roomName) {
                'Server Room' => "Smart-UPS Online Battery Backup 3000VA",
                'Admin'       => "Heavy-Duty Cross-Cut Document Shredder",
                default       => $roomName ? "{$roomName} Auxiliary Equipment #{$roomCount}" : "Auxiliary Hardware Unit #{$typeCount}",
            },
            default => $roomName ? "{$roomName} Equipment #{$roomCount}" : "Office Equipment Unit #{$typeCount}",
        };
    }

    /**
     * Replace sub-parts data with realistic mock data while preserving
     * parent device linkages (device_id) and status IDs.
     */
    private function mockDeviceParts(): void
    {
        $partCatalog = [
            'Monitor' => [
                'models' => [
                    'Dell E2422H 23.8" IPS FHD',
                    'HP P24v G4 23.8" Full HD',
                    'ASUS VA24EHE 23.8" Eye Care IPS',
                    'ViewSonic VA2432-H 24" IPS Frameless',
                    'AOC 24B2XH 23.8" Ultra Slim IPS',
                ],
                'specs' => '23.8-inch Full HD (1920x1080) IPS Anti-Glare, 75Hz, HDMI/VGA, Low Blue Light',
            ],
            'Keyboard' => [
                'models' => [
                    'Logitech K120 USB Keyboard',
                    'Dell KB216 Multimedia Keyboard',
                    'HP 125 Wired Desktop Keyboard',
                    'A4Tech ComfortKey KB-720 USB',
                ],
                'specs' => 'Wired USB Standard 104-Key Layout, Spill-Resistant, Low-Profile Keys',
            ],
            'Mouse' => [
                'models' => [
                    'Logitech B100 Optical USB Mouse',
                    'Dell MS116 Optical Mouse',
                    'HP 125 Wired Optical Mouse',
                    'A4Tech OP-620D USB Optical Mouse',
                ],
                'specs' => '1000 DPI Optical Sensor, USB Plug and Play, Ambidextrous 3-Button Design',
            ],
            'UPS' => [
                'models' => [
                    'APC Back-UPS BX650LI-MS 650VA',
                    'Eaton 5E 650VA USB 230V',
                    'CyberPower UT650EG 650VA Energy-Saving',
                    'Prolink PRO700SFC 650VA Super-Fast',
                ],
                'specs' => '650VA / 360W Line-Interactive AVR Battery Backup, 230V Universal Outlets',
            ],
            'Scanner' => [
                'models' => [
                    'Canon CanoScan LiDE 300 Flatbed',
                    'Epson Perfection V39 II Color Scanner',
                ],
                'specs' => '2400 x 2400 dpi Optical Resolution, USB Bus Powered, 4 EZ One-Touch Buttons',
            ],
            'Default' => [
                'models' => [
                    'Standard OEM Peripheral Module',
                    'Enterprise Workstation Accessory Unit',
                ],
                'specs' => 'Standard OEM Certified Hardware Accessory Unit',
            ],
        ];

        $parts = DevicePart::orderBy('id')->get();

        foreach ($parts as $part) {
            $partCategory = $this->determinePartCategory($part->name);
            $catalogEntry = $partCatalog[$partCategory] ?? $partCatalog['Default'];

            $models = $catalogEntry['models'];
            $selectedModel = $models[($part->id - 1) % count($models)];
            $selectedSpecs = $catalogEntry['specs'];

            // Realistic serial number for part
            $hashSuffix = strtoupper(substr(md5($part->id . '_dt_salt_part'), 0, 4));
            $newSerial = sprintf('SN-PRT-2026-%04d-%s', $part->id, $hashSuffix);

            // Realistic inventory number (preserves D3 standard format)
            $monthCode = sprintf('%02d', (($part->id % 12) + 1));
            $newInventory = sprintf('D3-2026-%s-%04d', $monthCode, $part->id);

            // Standardize part name
            $cleanName = match ($partCategory) {
                'Monitor'  => (stripos($part->name, 'dual') !== false || stripos($part->name, 'second') !== false)
                    ? 'Secondary Monitor'
                    : 'Main Monitor',
                'Keyboard' => 'Keyboard',
                'Mouse'    => 'Mouse',
                'UPS'      => 'UPS',
                'Scanner'  => 'Scanner',
                default    => $part->name ?: 'Peripheral Unit',
            };

            $part->name             = $cleanName;
            $part->model_num        = $selectedModel;
            $part->specs            = $selectedSpecs;
            $part->serial_number    = $newSerial;
            $part->inventory_number = $newInventory;
            $part->save();
        }
    }

    /**
     * Categorize a part name into a catalog key.
     */
    private function determinePartCategory(string $name): string
    {
        $lower = strtolower($name);
        if (str_contains($lower, 'monitor') || str_contains($lower, 'screen') || str_contains($lower, 'display')) {
            return 'Monitor';
        }
        if (str_contains($lower, 'keyboard') || str_contains($lower, 'kb')) {
            return 'Keyboard';
        }
        if (str_contains($lower, 'mouse') || str_contains($lower, 'trackball')) {
            return 'Mouse';
        }
        if (str_contains($lower, 'ups') || str_contains($lower, 'battery') || str_contains($lower, 'power backup')) {
            return 'UPS';
        }
        if (str_contains($lower, 'scanner') || str_contains($lower, 'scan')) {
            return 'Scanner';
        }
        return 'Default';
    }

    /**
     * Sanitize maintenance log descriptions with realistic professional IT notes.
     */
    private function mockMaintenanceLogs(): void
    {
        $descriptions = [
            'Conducted comprehensive preventive maintenance: cleaned internal chassis, blown dust from heatsink and power supply, reseated memory modules, and verified hardware health diagnostics.',
            'Replaced failing uninterruptible power supply (UPS) lead-acid battery cell. Validated line-interactive voltage stabilization and load transfer switch under simulated outage.',
            'Troubleshot system boot latency. Upgraded storage to high-speed NVMe solid state drive, performed clean OS installation, and restored standardized employee workstation profile.',
            'Replaced defective membrane keyboard and erratic optical mouse. Updated USB composite peripheral drivers and verified full input responsiveness.',
            'Cleaned print heads, lubricated carriage guides, and replaced pickup roller assembly. Performed test page calibration with zero paper feed errors.',
            'Investigated intermittent network connectivity issue. Re-crimped RJ-45 Cat6 modular patch connector and verified gigabit speed link negotiation.',
            'Applied quarterly firmware and security patch updates. Ran automated disk integrity scan and cleaned system temporary cache files.',
            'Addressed cooling fan noise: lubricated hydraulic bearing fan and applied high-conductivity thermal paste between CPU heat spreader and cooler.',
            'Calibrated color profile and refreshed display firmware on primary dual-monitor setup. Verified HDMI/DisplayPort cable signal stability.',
            'Conducted routine electrical safety and grounding inspection. Verified power strip surge suppression status and cable management routing.',
        ];

        $logs = MaintenanceLog::orderBy('id')->get();

        foreach ($logs as $log) {
            $log->description = $descriptions[($log->id - 1) % count($descriptions)];
            $log->save();
        }
    }

    /**
     * Synchronize device update logs with the new mock device names and realistic descriptions.
     *
     * @param array<int, string> $deviceNameMap
     */
    private function mockDeviceUpdateLogs(array $deviceNameMap): void
    {
        $logs = DeviceUpdateLog::orderBy('id')->get();

        foreach ($logs as $log) {
            if ($log->device_id && isset($deviceNameMap[$log->device_id])) {
                $log->device_name = $deviceNameMap[$log->device_id];
            }

            $log->description = match ($log->action) {
                'created'        => "Device '{$log->device_name}' was registered in the inventory database with standard hardware specifications.",
                'updated'        => "Updated hardware specifications and equipment configuration for '{$log->device_name}'.",
                'status_changed' => "Operational status of '{$log->device_name}' was updated following scheduled inspection.",
                'moved'          => "Relocated '{$log->device_name}' and updated room assignment in floor plan layout.",
                'part_added'     => "Attached new peripheral hardware component to '{$log->device_name}'.",
                'part_updated'   => "Updated peripheral component specifications for '{$log->device_name}'.",
                'part_deleted'   => "Removed peripheral component from '{$log->device_name}'.",
                'deleted'        => "Archived device record for '{$log->device_name}' from active registry.",
                'maintenance'    => "Performed scheduled preventive maintenance on '{$log->device_name}'.",
                default          => "Updated system records for device '{$log->device_name}'.",
            };

            if (!empty($log->changes) && is_array($log->changes)) {
                $sanitizedChanges = [];
                foreach ($log->changes as $field => $change) {
                    if (is_array($change)) {
                        $sanitizedChanges[$field] = [
                            'old' => is_string($change['old'] ?? null) ? 'Original Value' : ($change['old'] ?? null),
                            'new' => is_string($change['new'] ?? null) ? 'Updated Value' : ($change['new'] ?? null),
                        ];
                    } else {
                        $sanitizedChanges[$field] = 'Updated';
                    }
                }
                $log->changes = $sanitizedChanges;
            }

            $log->save();
        }
    }
};
