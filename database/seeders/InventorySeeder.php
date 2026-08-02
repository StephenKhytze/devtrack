<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DevicePart;
use App\Models\DeviceStatus;
use App\Models\Room;

class InventorySeeder extends Seeder
{
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

        // ============================================================
        // Each device below has a 'room' key: a room name (string) to
        // assign it to that room, or null to keep it standalone.
        // Standalone devices are spread across a placeholder grid on
        // the floor map; room-assigned devices default to position
        // 40/40 inside their room. Use the "Edit devices" drag feature
        // on the Floor Layout / Room Layout pages to place them
        // accurately afterward.
        // ============================================================

        $devices = [

            // ── Desktops (Computer's Inventory) ─────────────────

            [
                'name' => 'Cosico, Edmundo Jr.',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => '2409s331b0526',
                'inventory_number' => 'D5-2024-11-08-019',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz, 1TB M.2 NVMe SSD, 1x8GB DDR5 @4800MHz',
                'status' => 'good',
                'room' => 'Claims and Admin',
                'parts' => [
                    ['name' => 'Main Monitor', 'model_num' => 'HP N246V', 'inventory_number' => '081219IT0102513', 'serial_number' => '1cr9411lfg'],
                    ['name' => 'Dual Monitor', 'model_num' => 'Xitrix 24" FHD 100Hz IPS Monitor', 'inventory_number' => 'D3-2024-11-08-019', 'serial_number' => 'w2413sg247t1001200299m'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2024-11-05-019', 'serial_number' => 'k16524050495'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2024-11-01-019', 'serial_number' => 'm16524050495'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2024-11-02-019', 'serial_number' => '553701243800839'],
                ],
            ],
            [
                'name' => 'Mae Justine Hernandez',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ01853912d',
                'inventory_number' => 'D5-2026-05-08-094',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-094', 'serial_number' => 'W3LCBS004124'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-094', 'serial_number' => '0k10000603000KB26350f76'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-094', 'serial_number' => '0K100000919000MS261f0ebc'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-649', 'serial_number' => 'el6325b00698'],
                ],
            ],
            [
                'name' => 'Ferlo Talisic',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ01762612A',
                'inventory_number' => 'D5-2026-05-08-101',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Claims and Admin',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-101', 'serial_number' => 'W3LCBS004683'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-101', 'serial_number' => '0k10000603000KB262U033B'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-101', 'serial_number' => '0K100000919000MS2617035E'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-661', 'serial_number' => 'el6325b00710'],
                ],
            ],
            [
                'name' => 'Lydia Gay Asendido',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ017693122',
                'inventory_number' => 'D5-2026-05-08-100',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Claims and Admin',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-100', 'serial_number' => 'W3LCBS004945'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-100', 'serial_number' => '0k10000603000KB262U5C1'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-100', 'serial_number' => '0K100000919000MS261702BB'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-652', 'serial_number' => 'el6325b00701'],
                ],
            ],
            [
                'name' => 'Joshua Ademe',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ01832124',
                'inventory_number' => 'D5-2026-05-08-099',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Claims and Admin',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-099', 'serial_number' => 'W3LCBS004625'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-099', 'serial_number' => '0k10000603000KB26351001'],
                    // Note: source lists this mouse's inventory no. as "D2-2026-05-01-100" (likely a typo for -099) — transcribed as-is
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-100', 'serial_number' => '0K100000919000MS262E20D1'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-660', 'serial_number' => 'el6325b00709'],
                ],
            ],
            [
                'name' => 'Mariveth Alcala',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ018427126',
                'inventory_number' => 'D5-2026-05-08-098',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-098', 'serial_number' => 'W3LCBS004067'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-098', 'serial_number' => '0k10000603000KB263A00C7'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-098', 'serial_number' => '0K100000919000MS26V1524'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2023-02-02-140', 'serial_number' => '320063bv0000221'],
                ],
            ],
            [
                'name' => 'Eloisa Coronado',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ017813125',
                'inventory_number' => 'D5-2026-05-08-095',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-095', 'serial_number' => 'W3LCBS004659'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-095', 'serial_number' => '0k10000603000KB262u05bc'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-095', 'serial_number' => '0K100000919000MS262e1ad1'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-655', 'serial_number' => 'el6325b00704'],
                ],
            ],
            [
                'name' => 'Billy Jine Hernandez',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ018499123',
                'inventory_number' => 'D5-2026-05-08-093',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-093', 'serial_number' => 'W3LCBS004075'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-093', 'serial_number' => '0k10000603000KB26350ffa'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-093', 'serial_number' => '0K100000919000MS26261701fe'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-654', 'serial_number' => 'el6325b00703'],
                ],
            ],
            [
                'name' => 'Joann Rabulan',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                // Note: source lists the same serial as Mary Grace Mejillano below (likely a copy-paste error in the source sheet) — transcribed as-is
                'serial_number' => 'W3PFCJ017667129',
                'inventory_number' => 'D5-2026-05-08-092',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-092', 'serial_number' => 'W3LCBS003882'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-092', 'serial_number' => '0k10000603000KB26350fe9'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-092', 'serial_number' => '0K100000919000MS262p1373'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-664', 'serial_number' => 'el6325b00713'],
                ],
            ],
            [
                'name' => 'Mary Grace Mejillano',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ017667129',
                'inventory_number' => 'D5-2026-05-08-091',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-091', 'serial_number' => 'W3LCBS005197r4ez'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-091', 'serial_number' => '0k10000603000KB262u0239'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-091', 'serial_number' => '0K100000919000MS261f0a2'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-658', 'serial_number' => 'el6325b00707'],
                ],
            ],
            [
                'name' => 'Myra Gunda',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ018528127',
                'inventory_number' => 'D5-2026-05-08-103',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-103', 'serial_number' => 'W3LCBS004469'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-103', 'serial_number' => '0k10000603000KB26350dfb'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-103', 'serial_number' => '0K100000919000MS262e1eab'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-659', 'serial_number' => 'el6325b00708'],
                ],
            ],

            // ── Cashier PCs ──────────────────────────────────────

            [
                'name' => 'Darwin S. De Ramos (Cashier PC)',
                'type' => 'desktop',
                'model_num' => 'HP ProDesk 400 G2 MT',
                'serial_number' => 'SGH523POW9',
                'inventory_number' => '080715IT0101259',
                'specs' => 'Intel Core i5-4590 @3.30GHz, 8GB DDR3, 2GB dedicated GPU, 1TB storage',
                'status' => 'good',
                'room' => 'Cashier',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P231', 'inventory_number' => '08-0715IT0102259', 'serial_number' => 'CNC52108LT'],
                    ['name' => 'Mouse', 'model_num' => 'HP Mouse', 'inventory_number' => '08-0715IT0104238', 'serial_number' => 'FCYRV0AKZ8MIJ8'],
                    ['name' => 'Keyboard', 'model_num' => 'HP Keyboard', 'inventory_number' => '08-0715IT0103238', 'serial_number' => 'BCYUH0ACP7ZB0R'],
                    ['name' => 'Scanner', 'model_num' => 'CanoScan LIDE 110', 'inventory_number' => '08-0614IT1801003', 'serial_number' => 'KEFD92070'],
                ],
            ],
            [
                'name' => 'Maylene R. Gotis (Cashier PC)',
                'type' => 'desktop',
                'model_num' => 'Lenovo PC',
                'serial_number' => null,
                'inventory_number' => '08-1114IT0101113',
                'specs' => 'Intel Core i5-4590 @3.30GHz, 8GB DDR3, 2GB NVIDIA GeForce GT 730, 1TB storage',
                'status' => 'good',
                'room' => 'Cashier',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'Lenovo', 'inventory_number' => '08-1114IT0102113', 'serial_number' => 'V5347978'],
                    ['name' => 'Mouse', 'model_num' => 'Lenovo', 'inventory_number' => '08-1114IT0104113', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => 'Lenovo', 'inventory_number' => '08-1114IT0103113', 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => 'Lenovo', 'inventory_number' => '1308IT1301053', 'serial_number' => '1311000034PS25M'],
                ],
            ],

            // ── Back PCs of Cashier ──────────────────────────────

            [
                'name' => 'PC1 (Back PC of Cashier)',
                'type' => 'desktop',
                'model_num' => 'ASUS',
                'serial_number' => 'w3pfcj017884121',
                'inventory_number' => 'D520260508097',
                'specs' => 'Intel Core i3-14100, 16GB RAM, dedicated 2GB graphics card, 1TB storage',
                'status' => 'good',
                'room' => 'Cashier',
                'parts' => [
                    // Note: source left monitor model/inventory/serial blank — omitted
                    ['name' => 'Mouse', 'model_num' => 'ASUS', 'inventory_number' => 'D220260501097', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => 'ASUS', 'inventory_number' => 'D220260505097', 'serial_number' => '0K001-00603000KB262U05ED'],
                    ['name' => 'UPS', 'model_num' => 'ASUS', 'inventory_number' => 'D320251202663', 'serial_number' => 'EL6325B00712'],
                ],
            ],
            [
                'name' => 'PC2 (Back PC of Cashier)',
                'type' => 'desktop',
                'model_num' => 'Lenovo',
                'serial_number' => null,
                'inventory_number' => '081114IT0101110',
                'specs' => 'Intel Core i5-4590 @3.30GHz, 8GB DDR, 2GB GeForce GT 730, 1TB storage',
                'status' => 'good',
                'room' => 'Cashier',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ThinkVision', 'inventory_number' => '08-1114IT0102110', 'serial_number' => 'V5347967'],
                    ['name' => 'Mouse', 'model_num' => 'Lenovo', 'inventory_number' => '081114IT0104110', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => 'Lenovo', 'inventory_number' => '081114IT0103110', 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => 'Lenovo', 'inventory_number' => '081018IT1301500', 'serial_number' => null],
                ],
            ],
            [
                // Was previously a sub-part of PC2 ("Device in use") — now its own standalone device, same room as PC2
                'name' => 'Cashier Printer',
                'type' => 'printer',
                'model_num' => 'HP',
                'serial_number' => 'CNBKMBT0CC',
                'inventory_number' => '081219IT1407135',
                'specs' => null,
                'status' => 'good',
                'room' => 'Cashier',
                'parts' => [],
            ],
            [
                'name' => 'Mam Renalyn (Laptop)',
                'type' => 'laptop',
                'model_num' => 'Mid-Range',
                'serial_number' => 'C2507N0003484',
                'inventory_number' => '0520260201012',
                'specs' => null,
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Mam Aveth (Laptop)',
                'type' => 'laptop',
                'model_num' => 'Mid-Range',
                'serial_number' => 'C2507N0003573',
                'inventory_number' => '0520260201017',
                'specs' => null,
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],

            // ── Laptops ──────────────────────────────────────────
            // Laptops now use the dedicated 'laptop' type (added via migration).

            [
                'name' => 'John Paul Febrer (Laptop)',
                'type' => 'laptop',
                'model_num' => null,
                'serial_number' => 'c2507n0003472',
                'inventory_number' => '0520260201015',
                'specs' => 'Intel 13th Gen Core i5-1334U 1.30GHz, 16GB DDR4 3200MHz, 1TB storage',
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Mel Christopher Ramos (Laptop)',
                'type' => 'laptop',
                'model_num' => null,
                'serial_number' => 'c2507n0003505',
                'inventory_number' => '0520260201013',
                'specs' => 'Intel 13th Gen Core i5-1334U 1.30GHz, 16GB DDR4 3200MHz, 1TB storage',
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Redeemer Lonsamia (Laptop)',
                'type' => 'laptop',
                'model_num' => null,
                'serial_number' => 'c2507n0003486',
                'inventory_number' => '0520260201016',
                'specs' => 'Intel 13th Gen Core i5-1334U 1.30GHz, 16GB DDR4 3200MHz, 1TB storage',
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],

            // ── Printers, Aircons & Misc (Printers Inventory) ────
            // No status was ambiguous here — sheet explicitly listed Working/Maintenance/Out of Service.

            [
                'name' => 'Admin Backroom Printer',
                'type' => 'printer',
                'model_num' => 'HP LaserJet Enterprise M611dn',
                'serial_number' => 'phbrsdl26y',
                'inventory_number' => '080125IT1407207',
                'specs' => null,
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                // Note: labeled "Scanner" in source but description indicates an all-in-one printer
                'name' => 'Paims Scanner',
                'type' => 'printer',
                'model_num' => 'HP OfficeJet Pro 6830 (Colored, All-in-One)',
                'serial_number' => 'th5958102c',
                'inventory_number' => '081215IT1401015',
                'specs' => null,
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Paims Printer 1',
                'type' => 'printer',
                'model_num' => 'HP LaserJet Enterprise M607',
                'serial_number' => 'cnbkmbt0b0',
                'inventory_number' => '081219IT1407136',
                'specs' => null,
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Paims Printer 2',
                'type' => 'printer',
                'model_num' => 'HP LaserJet Enterprise M607',
                'serial_number' => 'cnbkmbt0cs',
                'inventory_number' => '081219IT1407138',
                'specs' => null,
                'status' => 'oos',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                'name' => 'Aircon Ceiling (near Paims and Collection)',
                'type' => 'aircon',
                'model_num' => 'LG Ceiling Aircon',
                'serial_number' => null,
                'inventory_number' => '0801260E402105',
                'specs' => null,
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                // Note: labeled "Scanner" in source but is an HP DeskJet Ink Advantage printer
                'name' => 'Mavieth Scanner',
                'type' => 'printer',
                'model_num' => 'HP DeskJet Ink Advantage 2337',
                'serial_number' => 'cn4bd420xq',
                'inventory_number' => 'D320251104060',
                'specs' => null,
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                'name' => 'Mavieth Printer',
                'type' => 'printer',
                'model_num' => 'HP Color LaserJet Pro M254nw',
                'serial_number' => 'vnc7y04355',
                'inventory_number' => '080120IT1408001',
                'specs' => null,
                'status' => 'maintenance',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                'name' => 'Standalone Aircon (near Cosico Table)',
                'type' => 'aircon',
                'model_num' => 'Carrier Airconditioner, 3.0 Ton, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '081217OE402026',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Standalone Aircon (near Mavieth Table)',
                'type' => 'aircon',
                'model_num' => 'Carrier Airconditioner, 3.0 Ton, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '081217OE402027',
                'specs' => null,
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                'name' => 'Shredder',
                'type' => 'other',
                'model_num' => 'Securio B34',
                'serial_number' => 'c56022187',
                'inventory_number' => '080119OE514011',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => "Printer (near Women's CR)",
                'type' => 'photocopier',
                'model_num' => 'Fuji Xerox DocuCentre-V 3065',
                'serial_number' => '546382',
                'inventory_number' => '081018IT406010',
                'specs' => null,
                'status' => 'oos',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Membership Printer',
                'type' => 'printer',
                'model_num' => 'HP LaserJet Enterprise M611dn',
                'serial_number' => 'phbrsdl25l',
                'inventory_number' => '08125IT1407208',
                'specs' => null,
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [],
            ],
            [
                // Note: source describes this as "Copier, Network Printer and Scanner" — classified as printer;
                // reclassify as 'photocopier' if the copier function is the primary use case
                'name' => 'Wellness Area Printer',
                'type' => 'printer',
                'model_num' => 'Lexmark Copier / Network Printer / Scanner',
                'serial_number' => '746652031017k',
                'inventory_number' => '0809250E513008',
                'specs' => null,
                'status' => 'good',
                'room' => 'Wellness Area',
                'parts' => [],
            ],
            [
                'name' => 'LG Aircon Window Type (near Hernandez Table)',
                'type' => 'aircon',
                'model_num' => 'LG Window-Type Aircon',
                'serial_number' => '412haspgy790',
                'inventory_number' => '0809040e02099',
                'specs' => null,
                'status' => 'good',
                'room' => 'Membership',
                'parts' => [],
            ],
        ];

        // ============================================================
        // Create everything. Standalone devices are spread across a
        // simple grid on the floor map; room-assigned devices default
        // to position 40/40 inside their room. Reposition precisely
        // afterward using "Edit devices" on the Floor Layout / Room
        // Layout pages.
        // ============================================================

        $standaloneIndex = 0;

        foreach ($devices as $d) {
            $roomId = null;
            $posX   = 40;
            $posY   = 40;

            if ($d['room'] !== null) {
                if (!isset($rooms[$d['room']])) {
                    throw new \Exception("InventorySeeder: room '{$d['room']}' not found for device '{$d['name']}'. Make sure RoomSeeder runs before InventorySeeder.");
                }
                $roomId = $rooms[$d['room']];
            } else {
                $col  = $standaloneIndex % 8;
                $row  = intdiv($standaloneIndex, 8);
                $posX = 6 + $col * 12;
                $posY = 6 + $row * 12;
                $standaloneIndex++;
            }

            $device = Device::create([
                'name'             => $d['name'],
                'type'             => $d['type'],
                'model_num'        => $d['model_num'],
                'serial_number'    => $d['serial_number'],
                'inventory_number' => $d['inventory_number'],
                'specs'            => $d['specs'],
                'sub_parts'        => !empty($d['parts']),
                'status_id'        => $statusMap[$d['status']],
                'room_id'          => $roomId,
                'pos_x'            => $posX,
                'pos_y'            => $posY,
            ]);

            foreach ($d['parts'] as $p) {
                DevicePart::create([
                    'device_id'        => $device->id,
                    'name'             => $p['name'],
                    'model_num'        => $p['model_num'],
                    'inventory_number' => $p['inventory_number'] ?? null,
                    'serial_number'    => $p['serial_number'] ?? null,
                    'status_id'        => $statusMap[$d['status']], // parts default to parent device's status
                ]);
            }
        }
    }
}
