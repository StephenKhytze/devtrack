<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DevicePart;
use App\Models\DeviceStatus;
use App\Models\DeviceUpdateLog;
use App\Models\MaintenanceLog;
use App\Models\Room;
use App\Models\User;

class MockInventorySeeder extends Seeder
{
    /**
     * Seed realistic fictional/mock inventory data that exactly matches
     * the 1:1 count of devices (72) and sub-parts (110) from the actual
     * dataset, preserving all spatial coordinates, room locations, and types.
     */
    public function run(): void
    {
        $good        = DeviceStatus::where('label', 'Good')->first();
        $maintenance = DeviceStatus::where('label', 'Maintenance')->first();
        $oos         = DeviceStatus::where('label', 'Out of Service')->first();

        // Room name => id lookup. RoomSeeder must run before this seeder.
        $rooms = Room::pluck('id', 'name');

        $statusMap = [
            'good'        => $good->id,
            'maintenance' => $maintenance->id,
            'oos'         => $oos->id,
        ];

        // Retrieve the full master blueprint of all 72 devices
        $masterDevices = InventorySeeder::getDevices();

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

        $standaloneIndex = 0;
        $deviceIndex     = 0;
        $partIndex       = 0;
        $personnelIndex  = 0;

        $roomCounters = [];
        $typeCounters = [];

        foreach ($masterDevices as $d) {
            $deviceIndex++;
            $roomId   = null;
            $posX     = 40;
            $posY     = 40;
            $roomName = $d['room'];

            if ($roomName !== null) {
                if (!isset($rooms[$roomName])) {
                    continue;
                }
                $roomId = $rooms[$roomName];
            } else {
                $col  = $standaloneIndex % 8;
                $row  = intdiv($standaloneIndex, 8);
                $posX = 6 + $col * 12;
                $posY = 6 + $row * 12;
                $standaloneIndex++;
            }

            $type = $d['type'] ?: 'desktop';
            $roomKey = $roomName ?? 'Standalone';
            $roomCounters[$roomKey] = ($roomCounters[$roomKey] ?? 0) + 1;
            $typeCounters[$type] = ($typeCounters[$type] ?? 0) + 1;

            $roomCount = $roomCounters[$roomKey];
            $typeCount = $typeCounters[$type];

            // Determine if the room is a storage room
            $targetRoom = $roomId ? Room::find($roomId) : null;
            $isStorage  = (bool)($targetRoom?->is_storage ?? false);

            // Generate realistic mock name
            $mockName = $this->generateMockName(
                $type,
                $roomName,
                $isStorage,
                $roomCount,
                $typeCount,
                $fictionalPersonnel,
                $personnelIndex
            );

            // Mock model & specs
            $modelList = $modelsByType[$type] ?? $modelsByType['desktop'];
            $specList  = $specsByType[$type] ?? $specsByType['desktop'];
            $mockModel = $modelList[($deviceIndex - 1) % count($modelList)];
            $mockSpecs = $specList[($deviceIndex - 1) % count($specList)];

            // Unique serial number and inventory number
            $typeCode = strtoupper(substr($type, 0, 3));
            $hashSuffix = strtoupper(substr(md5($deviceIndex . '_mock_device_salt'), 0, 4));
            $mockSerial = sprintf('SN-%s-2026-%04d-%s', $typeCode, $deviceIndex, $hashSuffix);

            $monthCode = sprintf('%02d', (($deviceIndex % 12) + 1));
            $mockInventory = sprintf('D5-2026-%s-%04d', $monthCode, $deviceIndex);

            $device = Device::create([
                'name'             => $mockName,
                'type'             => $type,
                'model_num'        => $mockModel,
                'serial_number'    => $mockSerial,
                'inventory_number' => $mockInventory,
                'specs'            => $mockSpecs,
                'sub_parts'        => !empty($d['parts']),
                'status_id'        => $statusMap[$d['status']] ?? $good->id,
                'room_id'          => $roomId,
                'pos_x'            => $posX,
                'pos_y'            => $posY,
            ]);

            // Create each sub-part with mock specifications
            foreach ($d['parts'] as $p) {
                $partIndex++;
                $partCategory = $this->categorizePart($p['name']);
                $catalogEntry = $partCatalog[$partCategory] ?? $partCatalog['Default'];

                $models = $catalogEntry['models'];
                $selectedModel = $models[($partIndex - 1) % count($models)];
                $selectedSpecs = $catalogEntry['specs'];

                $partHash = strtoupper(substr(md5($partIndex . '_mock_part_salt'), 0, 4));
                $partSerial = sprintf('SN-PRT-2026-%04d-%s', $partIndex, $partHash);
                $partMonth = sprintf('%02d', (($partIndex % 12) + 1));
                $partInventory = sprintf('D3-2026-%s-%04d', $partMonth, $partIndex);

                $cleanPartName = match ($partCategory) {
                    'Monitor'  => (stripos($p['name'], 'dual') !== false || stripos($p['name'], 'second') !== false)
                        ? 'Secondary Monitor'
                        : 'Main Monitor',
                    'Keyboard' => 'Keyboard',
                    'Mouse'    => 'Mouse',
                    'UPS'      => 'UPS',
                    'Scanner'  => 'Scanner',
                    default    => $p['name'] ?: 'Peripheral Unit',
                };

                DevicePart::create([
                    'device_id'        => $device->id,
                    'name'             => $cleanPartName,
                    'model_num'        => $selectedModel,
                    'inventory_number' => $partInventory,
                    'serial_number'    => $partSerial,
                    'specs'            => $selectedSpecs,
                    'status_id'        => $statusMap[$d['status']] ?? $good->id,
                ]);
            }
        }

        // Seed 10 mock devices for Local Office Storage & 10 for Regional Office Storage
        $this->seedStorageMockDevices($statusMap);

        // Seed 10 realistic mock maintenance logs
        $this->seedMockMaintenanceLogs($statusMap);
    }

    /**
     * Seed 10 devices for Local Office Storage and 10 for Regional Office Storage.
     */
    private function seedStorageMockDevices(array $statusMap): void
    {
        $localStorageRoom   = Room::where('name', 'Local Office Storage')->first();
        $regionalStorageRoom = Room::where('name', 'Regional Office Storage')->first();

        if (!$localStorageRoom || !$regionalStorageRoom) {
            return;
        }

        $localStorageDevices = [
            [
                'name'      => 'Spare Workstation PC #1 (Local Office Storage)',
                'type'      => 'desktop',
                'model'     => 'Dell OptiPlex 7090 Micro',
                'serial'    => 'SN-DES-2026-LOC-0001',
                'inv'       => 'D5-2026-05-0101',
                'specs'     => 'Intel Core i5-10400 @2.90GHz, 16GB DDR4, 512GB NVMe SSD, Intel UHD Graphics 630',
                'status'    => 'good',
                'parts'     => [
                    ['name' => 'Main Monitor', 'model' => 'Dell E2422H 23.8" IPS FHD', 'serial' => 'SN-PRT-2026-0201-AA11', 'inv' => 'D3-2026-05-0201', 'specs' => '23.8-inch Full HD (1920x1080) IPS Anti-Glare, 75Hz, HDMI/VGA', 'status' => 'good'],
                    ['name' => 'Keyboard', 'model' => 'Logitech K120 USB Keyboard', 'serial' => 'SN-PRT-2026-0202-AA12', 'inv' => 'D3-2026-05-0202', 'specs' => 'Wired USB Standard 104-Key Layout, Spill-Resistant, Low-Profile Keys', 'status' => 'good'],
                    ['name' => 'Mouse', 'model' => 'Dell MS116 Optical Mouse', 'serial' => 'SN-PRT-2026-0203-AA13', 'inv' => 'D3-2026-05-0203', 'specs' => '1000 DPI Optical Sensor, USB Plug and Play, Ambidextrous 3-Button Design', 'status' => 'good'],
                    ['name' => 'UPS', 'model' => 'CyberPower UT650EG 650VA', 'serial' => 'SN-PRT-2026-0204-AA14', 'inv' => 'D3-2026-05-0204', 'specs' => '650VA / 360W Line-Interactive AVR Battery Backup, 230V Universal Outlets', 'status' => 'good'],
                ],
            ],
            [
                'name'      => 'Spare Workstation PC #2 (Local Office Storage)',
                'type'      => 'desktop',
                'model'     => 'HP ProDesk 400 G7 SFF',
                'serial'    => 'SN-DES-2026-LOC-0002',
                'inv'       => 'D5-2026-06-0102',
                'specs'     => 'Intel Core i3-12100 3.3GHz, 8GB DDR4, 256GB SSD, Intel UHD 730',
                'status'    => 'maintenance',
                'parts'     => [
                    ['name' => 'Main Monitor', 'model' => 'HP P24v G4 23.8" Full HD', 'serial' => 'SN-PRT-2026-0205-AA15', 'inv' => 'D3-2026-06-0205', 'specs' => '23.8-inch Full HD (1920x1080) IPS Anti-Glare, 75Hz, HDMI/VGA', 'status' => 'good'],
                    ['name' => 'Keyboard', 'model' => 'HP 125 Wired Desktop Keyboard', 'serial' => 'SN-PRT-2026-0206-AA16', 'inv' => 'D3-2026-06-0206', 'specs' => 'Wired USB Standard 104-Key Layout, Spill-Resistant', 'status' => 'good'],
                    ['name' => 'Mouse', 'model' => 'HP 125 Wired Optical Mouse', 'serial' => 'SN-PRT-2026-0207-AA17', 'inv' => 'D3-2026-06-0207', 'specs' => '1000 DPI Optical Sensor, USB Plug and Play', 'status' => 'good'],
                ],
            ],
            [
                'name'      => 'Backup Service Laptop #1 (Local Office Storage)',
                'type'      => 'laptop',
                'model'     => 'Lenovo ThinkPad T14 Gen 3',
                'serial'    => 'SN-LAP-2026-LOC-0003',
                'inv'       => 'D5-2026-07-0103',
                'specs'     => 'Intel Core i5-1235U (10 cores, up to 4.4GHz), 16GB DDR4, 512GB NVMe SSD, 14" FHD IPS, Win 11 Pro',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Backup Service Laptop #2 (Local Office Storage)',
                'type'      => 'laptop',
                'model'     => 'Dell Latitude 5430',
                'serial'    => 'SN-LAP-2026-LOC-0004',
                'inv'       => 'D5-2026-07-0104',
                'specs'     => 'Intel Core i5-1245U, 16GB DDR4, 512GB SSD, 14" FHD Anti-Glare Display, Win 11 Pro',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Surplus Office Printer #1 (Local Office Storage)',
                'type'      => 'printer',
                'model'     => 'HP LaserJet Pro MFP 4103fdw',
                'serial'    => 'SN-PRI-2026-LOC-0005',
                'inv'       => 'D5-2026-08-0105',
                'specs'     => 'Monochrome Laser Multi-function, 40 ppm, Auto 2-sided printing, Gigabit Ethernet & Wi-Fi',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Defective Dot Matrix Printer (Local Office Storage)',
                'type'      => 'printer',
                'model'     => 'Epson LQ-310 24-Pin',
                'serial'    => 'SN-PRI-2026-LOC-0006',
                'inv'       => 'D5-2026-08-0106',
                'specs'     => '24-Pin Narrow Carriage Impact Dot Matrix Printer, 347 cps, USB 2.0 & Parallel interface',
                'status'    => 'oos',
                'parts'     => [],
            ],
            [
                'name'      => 'Reserve Telephone Handset #1 (Local Office Storage)',
                'type'      => 'telephone',
                'model'     => 'Cisco IP Phone 7841 Multi-Line',
                'serial'    => 'SN-TEL-2026-LOC-0007',
                'inv'       => 'D5-2026-09-0107',
                'specs'     => '4-Line Gigabit IP Telephone, Backlit Graphical LCD, Dual Gigabit Ethernet, PoE enabled',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Spare Monitor Unit #1 (Local Office Storage)',
                'type'      => 'monitor',
                'model'     => 'Dell E2422H 24" FHD IPS Display',
                'serial'    => 'SN-MON-2026-LOC-0008',
                'inv'       => 'D5-2026-09-0108',
                'specs'     => '23.8-inch Full HD (1920x1080) IPS Anti-Glare, 75Hz, HDMI/VGA, VESA Mount 100x100mm',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Archived Photocopier Unit (Local Office Storage)',
                'type'      => 'photocopier',
                'model'     => 'Canon imageRUNNER 2625i Multi-Function',
                'serial'    => 'SN-PHO-2026-LOC-0009',
                'inv'       => 'D5-2026-10-0109',
                'specs'     => 'Monochrome Laser A3 MFP, Print / Copy / Color Scan, 25 ppm, Duplex Automatic Document Feeder',
                'status'    => 'maintenance',
                'parts'     => [],
            ],
            [
                'name'      => 'Decommissioned Split AC Unit (Local Office Storage)',
                'type'      => 'aircon',
                'model'     => 'Condura Prima Inverter 1.5HP',
                'serial'    => 'SN-AIR-2026-LOC-0010',
                'inv'       => 'D5-2026-10-0110',
                'specs'     => '1.5 HP Inverter Split Type AC, R32 Eco Refrigerant, Anti-Bacterial Filter (Pending Disposal)',
                'status'    => 'oos',
                'parts'     => [],
            ],
        ];

        $regionalStorageDevices = [
            [
                'name'      => 'Regional Reserve Server Unit (Regional Office Storage)',
                'type'      => 'network',
                'model'     => 'Cisco Catalyst 2960-X 24-Port',
                'serial'    => 'SN-NET-2026-REG-0001',
                'inv'       => 'D5-2026-01-0201',
                'specs'     => '24x Gigabit Ethernet Ports, 4x 1G SFP Uplinks, 370W PoE+ Power Budget, Layer 2 Switching',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Regional Buffer Desktop PC #1 (Regional Office Storage)',
                'type'      => 'desktop',
                'model'     => 'ASUS ExpertCenter D5 SFF (D500SC)',
                'serial'    => 'SN-DES-2026-REG-0002',
                'inv'       => 'D5-2026-02-0202',
                'specs'     => 'Intel Core i5-12400 2.5GHz, 16GB DDR4, 512GB M.2 PCIe NVMe SSD, Gigabit LAN, Win 11 Pro',
                'status'    => 'good',
                'parts'     => [
                    ['name' => 'Main Monitor', 'model' => 'ASUS VA24EHE 23.8" Eye Care IPS', 'serial' => 'SN-PRT-2026-0208-BB21', 'inv' => 'D3-2026-02-0208', 'specs' => '23.8" Full HD 1080p IPS, 75Hz, FreeSync, Low Blue Light', 'status' => 'good'],
                    ['name' => 'Keyboard', 'model' => 'Logitech K120 USB Keyboard', 'serial' => 'SN-PRT-2026-0209-BB22', 'inv' => 'D3-2026-02-0209', 'specs' => 'Wired USB Standard 104-Key Layout, Spill-Resistant', 'status' => 'good'],
                    ['name' => 'Mouse', 'model' => 'Logitech B100 Optical USB Mouse', 'serial' => 'SN-PRT-2026-0210-BB23', 'inv' => 'D3-2026-02-0210', 'specs' => '1000 DPI Optical Sensor, USB Plug and Play', 'status' => 'good'],
                    ['name' => 'UPS', 'model' => 'APC Back-UPS BX650LI-MS 650VA', 'serial' => 'SN-PRT-2026-0211-BB24', 'inv' => 'D3-2026-02-0211', 'specs' => '650VA / 390W Battery Backup with AVR Protection', 'status' => 'good'],
                ],
            ],
            [
                'name'      => 'Regional Buffer Desktop PC #2 (Regional Office Storage)',
                'type'      => 'desktop',
                'model'     => 'Lenovo ThinkCentre M70s Gen 3',
                'serial'    => 'SN-DES-2026-REG-0003',
                'inv'       => 'D5-2026-02-0203',
                'specs'     => 'Intel Core i3-12100 3.3GHz, 8GB DDR4, 512GB SSD, Intel UHD Graphics 730, Win 11 Pro',
                'status'    => 'good',
                'parts'     => [
                    ['name' => 'Main Monitor', 'model' => 'ViewSonic VA2432-H 24" IPS Frameless', 'serial' => 'SN-PRT-2026-0212-BB25', 'inv' => 'D3-2026-02-0212', 'specs' => '24" Full HD 1080p IPS, Frameless Design, HDMI/VGA', 'status' => 'good'],
                    ['name' => 'Keyboard', 'model' => 'A4Tech ComfortKey KB-720 USB', 'serial' => 'SN-PRT-2026-0213-BB26', 'inv' => 'D3-2026-02-0213', 'specs' => 'Standard USB Wired Keyboard with Laser Engraved Keys', 'status' => 'good'],
                    ['name' => 'Mouse', 'model' => 'A4Tech OP-620D USB Optical Mouse', 'serial' => 'SN-PRT-2026-0214-BB27', 'inv' => 'D3-2026-02-0214', 'specs' => '1000 DPI Optical Sensor, 2x Click Button, USB', 'status' => 'good'],
                ],
            ],
            [
                'name'      => 'Regional Floating Laptop #1 (Regional Office Storage)',
                'type'      => 'laptop',
                'model'     => 'HP ProBook 450 G9',
                'serial'    => 'SN-LAP-2026-REG-0004',
                'inv'       => 'D5-2026-03-0204',
                'specs'     => 'Intel Core i5-1235U, 16GB RAM, 512GB SSD, 15.6" FHD IPS Anti-Glare, Windows 11 Pro',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Regional Floating Laptop #2 (Regional Office Storage)',
                'type'      => 'laptop',
                'model'     => 'ASUS ExpertBook B1 (B1502)',
                'serial'    => 'SN-LAP-2026-REG-0005',
                'inv'       => 'D5-2026-03-0205',
                'specs'     => 'Intel Core i7-1255U, 16GB DDR4, 1TB PCIe NVMe SSD, 15.6" FHD Display, Fingerprint Reader',
                'status'    => 'maintenance',
                'parts'     => [],
            ],
            [
                'name'      => 'Heavy Duty Network Laser Printer (Regional Office Storage)',
                'type'      => 'printer',
                'model'     => 'Brother HL-L5200DW Enterprise Laser',
                'serial'    => 'SN-PRI-2026-REG-0006',
                'inv'       => 'D5-2026-04-0206',
                'specs'     => 'High-speed duplex laser, 42 ppm, Gigabit Ethernet & Wireless, 520-sheet paper capacity',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Standby IP Telephone Handset #1 (Regional Office Storage)',
                'type'      => 'telephone',
                'model'     => 'Yealink SIP-T31P HD VoIP Phone',
                'serial'    => 'SN-TEL-2026-REG-0007',
                'inv'       => 'D5-2026-04-0207',
                'specs'     => '2-Line HD Voice VoIP Phone, Dual 10/100M Ethernet, PoE enabled, Opus Audio Codec',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Standby IP Telephone Handset #2 (Regional Office Storage)',
                'type'      => 'telephone',
                'model'     => 'Grandstream GRP2614 IP Phone',
                'serial'    => 'SN-TEL-2026-REG-0008',
                'inv'       => 'D5-2026-05-0208',
                'specs'     => '4-Line Carrier-Grade IP Phone, Dual Color LCD Screens, Integrated Dual-Band Wi-Fi',
                'status'    => 'good',
                'parts'     => [],
            ],
            [
                'name'      => 'Archived Heavy-Duty Copier (Regional Office Storage)',
                'type'      => 'photocopier',
                'model'     => 'Kyocera TASKalfa 2554ci Heavy Duty',
                'serial'    => 'SN-PHO-2026-REG-0009',
                'inv'       => 'D5-2026-05-0209',
                'specs'     => 'A3 Color Multifunction Copier, 25/25 ppm, 1200x1200 dpi, 10.1" Touchscreen, Dual Scan Processor',
                'status'    => 'oos',
                'parts'     => [],
            ],
            [
                'name'      => 'Surplus Rackmount UPS Unit (Regional Office Storage)',
                'type'      => 'other',
                'model'     => 'APC Smart-UPS RT 3000VA 230V SURT3000XLI',
                'serial'    => 'SN-OTH-2026-REG-0010',
                'inv'       => 'D5-2026-06-0210',
                'specs'     => '3000VA / 2100W On-Line Double Conversion UPS, 230V, LCD Status Display, SmartSlot Interface',
                'status'    => 'maintenance',
                'parts'     => [],
            ],
        ];

        // Seed Local Office Storage devices
        foreach ($localStorageDevices as $data) {
            $device = Device::create([
                'name'             => $data['name'],
                'type'             => $data['type'],
                'model_num'        => $data['model'],
                'serial_number'    => $data['serial'],
                'inventory_number' => $data['inv'],
                'specs'            => $data['specs'],
                'sub_parts'        => !empty($data['parts']),
                'status_id'        => $statusMap[$data['status']],
                'room_id'          => $localStorageRoom->id,
                'pos_x'            => 0,
                'pos_y'            => 0,
            ]);

            foreach ($data['parts'] as $p) {
                DevicePart::create([
                    'device_id'        => $device->id,
                    'name'             => $p['name'],
                    'model_num'        => $p['model'],
                    'serial_number'    => $p['serial'],
                    'inventory_number' => $p['inv'],
                    'specs'            => $p['specs'],
                    'status_id'        => $statusMap[$p['status']],
                ]);
            }
        }

        // Seed Regional Office Storage devices
        foreach ($regionalStorageDevices as $data) {
            $device = Device::create([
                'name'             => $data['name'],
                'type'             => $data['type'],
                'model_num'        => $data['model'],
                'serial_number'    => $data['serial'],
                'inventory_number' => $data['inv'],
                'specs'            => $data['specs'],
                'sub_parts'        => !empty($data['parts']),
                'status_id'        => $statusMap[$data['status']],
                'room_id'          => $regionalStorageRoom->id,
                'pos_x'            => 0,
                'pos_y'            => 0,
            ]);

            foreach ($data['parts'] as $p) {
                DevicePart::create([
                    'device_id'        => $device->id,
                    'name'             => $p['name'],
                    'model_num'        => $p['model'],
                    'serial_number'    => $p['serial'],
                    'inventory_number' => $p['inv'],
                    'specs'            => $p['specs'],
                    'status_id'        => $statusMap[$p['status']],
                ]);
            }
        }
    }

    /**
     * Seed 10 realistic maintenance log entries.
     */
    private function seedMockMaintenanceLogs(array $statusMap): void
    {
        $staff1 = User::where('username', 'alex.cruz')->first();
        $staff2 = User::where('username', 'maria.santos')->first();
        $admin  = User::where('username', 'admin')->first();

        $defaultUser = $staff1 ?? $admin ?? User::first();
        $user1 = $staff1 ?? $defaultUser;
        $user2 = $staff2 ?? $defaultUser;

        $logsData = [
            [
                'device_search' => 'Alexander M. Cruz',
                'user'          => $user1,
                'date'          => '2026-09-15',
                'deadline'      => '2026-09-18',
                'status_before' => $statusMap['maintenance'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Replaced malfunctioning secondary monitor HDMI cable and re-seated RAM modules. Passed 64-bit memory diagnostic tests.',
            ],
            [
                'device_search' => 'Dot Matrix Receipt Printer (Cashier',
                'user'          => $user2,
                'date'          => '2026-09-20',
                'deadline'      => '2026-09-22',
                'status_before' => $statusMap['oos'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Cleared paper jam in tractor feed mechanism, cleaned printhead carriage rails with isopropanol, and installed fresh ribbon cartridge.',
            ],
            [
                'device_search' => 'Administrative Laser Printer',
                'user'          => $user1,
                'date'          => '2026-09-28',
                'deadline'      => '2026-10-10',
                'status_before' => $statusMap['good'],
                'status_after'  => $statusMap['maintenance'],
                'description'   => 'Faint horizontal streaks appearing on output. Ordered replacement imaging drum unit; printer is in low-volume temporary use.',
            ],
            [
                'device_search' => 'Server Room Inverter AC Unit',
                'user'          => $user2,
                'date'          => '2026-10-01',
                'deadline'      => '2026-10-02',
                'status_before' => $statusMap['maintenance'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Conducted quarterly chemical cleaning of evaporator coils, checked refrigerant pressure (R32), and verified thermostat calibration.',
            ],
            [
                'device_search' => 'Camille Joyce Soriano',
                'user'          => $user1,
                'date'          => '2026-10-02',
                'deadline'      => '2026-10-04',
                'status_before' => $statusMap['oos'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Power supply unit (PSU) failure caused system shutdown. Swapped with new 300W 80+ certified PSU, updated OS security patches.',
            ],
            [
                'device_search' => 'Spare Workstation PC #2 (Local Office Storage)',
                'user'          => $user1,
                'date'          => '2026-10-03',
                'deadline'      => '2026-10-15',
                'status_before' => $statusMap['oos'],
                'status_after'  => $statusMap['maintenance'],
                'description'   => 'Diagnosed faulty SATA SSD sector errors. Removed drive and requested requisition for 500GB NVMe SSD replacement.',
            ],
            [
                'device_search' => 'Regional Floating Laptop #2',
                'user'          => $user2,
                'date'          => '2026-10-04',
                'deadline'      => '2026-10-12',
                'status_before' => $statusMap['good'],
                'status_after'  => $statusMap['maintenance'],
                'description'   => 'Intermittent spacebar and backspace key unresponsive. Sent request to authorized service vendor for keyboard module assembly.',
            ],
            [
                'device_search' => 'Core Distribution Switch 24-Port',
                'user'          => $user1,
                'date'          => '2026-10-05',
                'deadline'      => '2026-10-05',
                'status_before' => $statusMap['maintenance'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Upgraded RouterOS firmware to latest LTS release. Tested PoE negotiation across ports 1-16; verified VLAN 10 & 20 routing.',
            ],
            [
                'device_search' => 'Contributions Workgroup Printer #7',
                'user'          => $user2,
                'date'          => '2026-10-06',
                'deadline'      => '2026-10-08',
                'status_before' => $statusMap['maintenance'],
                'status_after'  => $statusMap['oos'],
                'description'   => 'Main logic board communication error 0x7E detected. Hardware replacement cost exceeds unit depreciation value; recommended for unserviceable scrap.',
            ],
            [
                'device_search' => 'Central Office Multi-Function Photocopier',
                'user'          => $user1,
                'date'          => '2026-10-07',
                'deadline'      => '2026-10-07',
                'status_before' => $statusMap['maintenance'],
                'status_after'  => $statusMap['good'],
                'description'   => 'Replaced developer unit and primary corona wire. Executed automated color calibration and multi-sheet feeder diagnostic run.',
            ],
        ];

        foreach ($logsData as $ld) {
            $device = Device::where('name', 'like', "%{$ld['device_search']}%")->first();
            if (!$device) {
                $device = Device::first();
            }

            MaintenanceLog::create([
                'device_id'        => $device->id,
                'performed_by'     => $ld['user']->id,
                'date'             => $ld['date'],
                'deadline'         => $ld['deadline'],
                'description'      => $ld['description'],
                'status_before_id' => $ld['status_before'],
                'status_after_id'  => $ld['status_after'],
            ]);

            DeviceUpdateLog::record(
                $device,
                'maintenance',
                "Maintenance logged by {$ld['user']->username}: {$ld['description']}",
                [
                    'status_before_id' => $ld['status_before'],
                    'status_after_id'  => $ld['status_after'],
                    'date'             => $ld['date'],
                    'deadline'         => $ld['deadline'],
                ],
                $ld['user']
            );
        }
    }

    /**
     * Generate a realistic contextual device name.
     */
    private function generateMockName(
        string $type,
        ?string $roomName,
        bool $isStorage,
        int $roomCount,
        int $typeCount,
        array $fictionalPersonnel,
        int &$personnelIndex
    ): string {
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
     * Categorize a part name.
     */
    private function categorizePart(string $name): string
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
}
