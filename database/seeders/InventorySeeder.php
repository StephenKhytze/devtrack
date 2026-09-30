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
                'room' => 'IT and Claims',
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
                'room' => 'IT and Claims',
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
                'room' => 'IT and Claims',
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
                'room' => 'IT and Claims',
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
                'room' => 'IT and Claims',
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
                'name' => 'Standalone Aircon (near Mariveth Table)',
                'type' => 'aircon',
                'model_num' => 'Carrier Airconditioner, 3.0 Ton, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '081217OE402027',
                'specs' => null,
                'status' => 'good',
                'room' => null,
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
                'name' => "Photocopier (near Women's CR)",
                'type' => 'photocopier',
                'model_num' => 'Fuji Xerox DocuCentre-V 3065',
                'serial_number' => '546382',
                'inventory_number' => '081018IT406010',
                'specs' => null,
                'status' => 'good',
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

            // ── Telephones, Laptop, Pantry Appliances ────────────

            [
                'name' => 'Mariveth Telephone',
                'type' => 'telephone',
                'model_num' => 'PABX - IP Phone 9611G',
                'serial_number' => '16wz304002xp',
                'inventory_number' => '19-0117ce0403017',
                'specs' => null,
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                'name' => 'Mariveth Laptop',
                'type' => 'laptop',
                'model_num' => null,
                'serial_number' => 'c2507n0003573',
                'inventory_number' => '0520260201017',
                'specs' => 'Intel 13th Gen Core i5-1334U 1.30GHz, 16GB DDR4 3200MHz, 1TB storage',
                'status' => 'good',
                'room' => 'Admin',
                'parts' => [],
            ],
            [
                'name' => 'Paims Telephone',
                'type' => 'telephone',
                'model_num' => 'PABX - IP Phone 9611G',
                'serial_number' => '16wz3040012j',
                'inventory_number' => '19-0117ce0403005',
                'specs' => null,
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [],
            ],
            [
                // Note: "Pantry" is a new room — must be added via RoomSeeder or
                // the Add Room modal before this seeder is run.
                'name' => 'Pantry Water Dispenser',
                'type' => 'appliance',
                'model_num' => 'Mitsutech MWD-132 Hot & Cold Water Dispenser',
                'serial_number' => '18020242',
                'inventory_number' => 'F320181217001',
                'specs' => null,
                'status' => 'good',
                'room' => 'Pantry',
                'parts' => [],
            ],
            [
                'name' => 'Pantry Refrigerator',
                'type' => 'appliance',
                'model_num' => 'Sharp SJ-DT70AS-SL, 6 cu ft, Silver Single Door',
                'serial_number' => '201606S7000482',
                'inventory_number' => '0809166oe406004',
                'specs' => null,
                'status' => 'good',
                'room' => 'Pantry',
                'parts' => [],
            ],

            // ── Frontline PCs (Room: Front Desk) ─────────────────

            [
                'name' => 'Counter 5 PC',
                'type' => 'desktop',
                'model_num' => 'HP ProDesk 600 G4 MT',
                'serial_number' => '4ce9450jtq',
                'inventory_number' => '081219it0101513',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P231', 'inventory_number' => '080715it0102226', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '080715it0103226', 'serial_number' => null],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '080715it0104226', 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => '080715it1301229', 'serial_number' => null],
                ],
            ],
            [
                'name' => 'Counter 6 PC',
                'type' => 'desktop',
                'model_num' => 'HP ProDesk 400 MT',
                'serial_number' => 'sgh523p0w5',
                'inventory_number' => '080715it0101246',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P231', 'inventory_number' => null, 'serial_number' => 'cnc52108lp'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '080715it0103230', 'serial_number' => 'bcyun0acp7z510'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => null, 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'd320251202650', 'serial_number' => 'el6325b00699'],
                ],
            ],
            [
                'name' => 'Counter 7 PC',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'wp3fcj018365127',
                'inventory_number' => 'd520260508090',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P231', 'inventory_number' => '080715it0102228', 'serial_number' => 'cnc52108m1'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'd220260505090', 'serial_number' => '0k001-00603000kb2635ofa5'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'd220260501090', 'serial_number' => '0K100000919000MS262e2060'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'd320251202666', 'serial_number' => 'el6325b00715'],
                ],
            ],
            [
                'name' => 'Counter 8',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ01767412g',
                'inventory_number' => 'D5-2026-05-08-102',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-102', 'serial_number' => 'W3LCBS004074'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-102', 'serial_number' => '0k10000603000KB262U0586'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-102', 'serial_number' => '0K100000919000MS266170260'],
                    // Note: source lists no UPS for this unit
                ],
            ],
            [
                'name' => 'Counter 9',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ017886128',
                'inventory_number' => 'D5-2026-05-08-096',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-096', 'serial_number' => 'W3LCBS004636'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-096', 'serial_number' => '0k10000603000KB26u05bf'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-104', 'serial_number' => '0K100000919000MS261f0ec4'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D320240102171', 'serial_number' => null],
                ],
            ],
            [
                'name' => 'Counter 12',
                'type' => 'desktop',
                'model_num' => 'HP ProDesk 600 G2 MT',
                'serial_number' => 'sgh523p0xn',
                'inventory_number' => '080715it0101299',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ViewSonic TD2220-2', 'inventory_number' => '0801171it0102390', 'serial_number' => 'tx5163800061'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '12pc363', 'serial_number' => 'bbuvl0mga1t85j'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '080715it0104299', 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => '080715it1301230', 'serial_number' => 'ga64e45005'],
                ],
            ],
            [
                'name' => 'Counter 15',
                'type' => 'desktop',
                'model_num' => 'HP ProDesk 600 G2 MT',
                'serial_number' => 'sgh2523p0qd',
                'inventory_number' => '080715it0101230',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P231', 'inventory_number' => '080715it0102231', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '080715it0103231', 'serial_number' => null],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '080715it0104231', 'serial_number' => null],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'd320230202141', 'serial_number' => '320063bv0000222'],
                ],
            ],
            [
                'name' => 'Priority Lane',
                'type' => 'desktop',
                'model_num' => 'ASUS S5 SFF (S501SER)',
                'serial_number' => 'W3PFCJ018431129',
                'inventory_number' => 'D5-2026-05-08-04',
                'specs' => 'Intel Core i3-14100 Processor 3.5GHz (12MB Cache, up to 4.7GHz, 4 cores, 8 threads), 1TB M.2 NVMe SSD, 2x8GB DDR4 @3200MHz',
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'ASUS ExpertCenter C221HF 23.8"', 'inventory_number' => 'D3-2026-05-08-103', 'serial_number' => 'W3LCBS00460'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => 'D2-2026-05-05-104', 'serial_number' => '0k10000603000KB262U0552'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => 'D2-2026-05-01-104', 'serial_number' => '0K100000919000MS262E20D9'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'D3-2025-12-02-656', 'serial_number' => 'el6325b00705'],
                ],
            ],

            // ── Frontline standalone printers, monitors, aircons ─

            [
                'name' => 'Printer (back of Counter 8)',
                'type' => 'printer',
                'model_num' => 'HP LaserJet Enterprise M607',
                'serial_number' => 'cnbkm3q5gm',
                'inventory_number' => '081219it1407137',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [],
            ],
            [
                'name' => 'Printer (back of Counter 9)',
                'type' => 'printer',
                'model_num' => 'Lexmark MS823DN',
                'serial_number' => '406423211v13x',
                'inventory_number' => '080523it1407158',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [],
            ],
            [
                // Note: source lists the same serial as the printer above — likely a
                // copy-paste error in the source sheet; transcribed as-is
                'name' => 'Printer (between Counter 15 and Priority Lane)',
                'type' => 'printer',
                'model_num' => 'Lexmark MS823DN',
                'serial_number' => '406423211v13x',
                'inventory_number' => '080523it1407157',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [],
            ],
            [
                'name' => 'Frontline Queuing Monitor (Counter 5)',
                'type' => 'monitor',
                'model_num' => null,
                'serial_number' => '00nsanpm900036',
                'inventory_number' => '081219it3402006',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [],
            ],
            [
                'name' => 'Frontline Queuing Monitor (between Counter 9 & 10)',
                'type' => 'monitor',
                'model_num' => null,
                'serial_number' => '106100009385',
                'inventory_number' => 'h320251113016',
                'specs' => null,
                'status' => 'good',
                'room' => 'Front Desk',
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 1 (near Cashier)',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402072',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 2',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402073',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 3',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402074',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 4',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402075',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 5',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402077',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],
            [
                'name' => 'Front Line Aircon 6 (near breast feeding area)',
                'type' => 'aircon',
                'model_num' => 'Green GV-36-6DR-I, 4HP, Floor Mounted',
                'serial_number' => null,
                'inventory_number' => '0801240e0402076',
                'specs' => null,
                'status' => 'good',
                'room' => null,
                'parts' => [],
            ],

            // ── Contributions / LHIO Head Desktops ───────────────

            [
                'name' => 'Aleona Rosales',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => '4ce942iMMK',
                'inventory_number' => '081219IT0101558',
                'specs' => 'Intel Core i5-8500 CPU @3.00GHz, 8GB RAM, 1TB Storage',
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP N246V', 'inventory_number' => '081219it0102558', 'serial_number' => '1cr9411krz'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '081219it0103558', 'serial_number' => 'k16524050495'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '081219it0104558', 'serial_number' => 'fcmhh0a67cspw5'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => '081219IT1301558', 'serial_number' => 'PA63j22ndt'],
                ],
            ],
            [
                'name' => 'Jerome Garovillas',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => '4ce942immc',
                'inventory_number' => '081219IT0101566',
                'specs' => 'Intel Core i5-8500 CPU @3.00GHz, 8GB RAM, 1TB Storage',
                'status' => 'good',
                'room' => 'Contributions',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP N246V', 'inventory_number' => '0812191T0102566', 'serial_number' => '1cr9411kt4'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '081219it0103566', 'serial_number' => 'bcyru0blacq8js'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '081219it0104566', 'serial_number' => 'fcmhh0a67cspvl'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => '080815it1302012', 'serial_number' => 'at650311506230570'],
                ],
            ],
            [
                // Note: Serial Number and Inventory Number appear swapped in the
                // source (this device's "Serial Number" matches the IT-prefixed
                // inventory-number pattern used everywhere else). Transcribed
                // exactly as labeled in the source — verify against the physical unit.
                'name' => 'Grace Ramilo',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => '080417it0101343',
                'inventory_number' => 'spc0hwza0',
                'specs' => 'Intel Core i5-8500 CPU @3.00GHz, 8GB RAM, 1TB Storage',
                'status' => 'good',
                'room' => 'LHIO Head',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP N246V', 'inventory_number' => '0812191T0102513', 'serial_number' => '1cr9411krw'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '080417it0103343', 'serial_number' => '9809068'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '080417it0104343', 'serial_number' => '161106629653'],
                    // Note: source lists this UPS with the identical inventory/serial
                    // number as Garovillas' UPS above — likely a copy-paste duplicate
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => '080815it1302012', 'serial_number' => 'at650311506230570'],
                ],
            ],
            [
                'name' => 'Aircon (LHIO Head)',
                'type' => 'aircon',
                'model_num' => null,
                'serial_number' => null,
                'inventory_number' => '0801240e0402078',
                'specs' => null,
                'status' => 'good',
                'room' => 'LHIO Head',
                'parts' => [],
            ],

            // ── Conference Room equipment ─────────────────────────
            // Note: "Conference Room" is a new room — must be added via
            // RoomSeeder or the Add Room modal before this seeder is run.

            [
                'name' => 'Conference Room Aircon 1',
                'type' => 'aircon',
                'model_num' => 'Koppel Inverter Type 3TR FM Inverter',
                'serial_number' => 'gm587341',
                'inventory_number' => '0811190E0402039',
                'specs' => null,
                'status' => 'good',
                'room' => 'Conference Room',
                'parts' => [],
            ],
            [
                'name' => 'Conference Room Speaker',
                'type' => 'appliance',
                'model_num' => 'LTO Professional TS415',
                'serial_number' => null,
                'inventory_number' => '0811240e0101003',
                'specs' => null,
                'status' => 'good',
                'room' => 'Conference Room',
                'parts' => [],
            ],
            [
                'name' => 'Conference Room Aircon 2',
                'type' => 'aircon',
                'model_num' => 'Koppel Inverter Type 3TR FM Inverter',
                'serial_number' => null,
                'inventory_number' => '0801240e402071',
                'specs' => null,
                'status' => 'good',
                'room' => 'Conference Room',
                'parts' => [],
            ],
            [
                'name' => 'Conference Room Microphone',
                'type' => 'appliance',
                'model_num' => 'Shure SVX4',
                'serial_number' => null,
                'inventory_number' => '0808140e108012',
                'specs' => null,
                'status' => 'good',
                'room' => 'Conference Room',
                'parts' => [],
            ],
            [
                'name' => 'Conference Room TV',
                'type' => 'appliance',
                'model_num' => 'Samsung Smart TV 60"',
                'serial_number' => null,
                'inventory_number' => '080516oe0407013',
                'specs' => null,
                'status' => 'good',
                'room' => 'Conference Room',
                'parts' => [],
            ],

            // ── Server Room ───────────────────────────────────────

            [
                'name' => 'Server',
                'type' => 'network',
                'model_num' => null,
                'serial_number' => null,
                'inventory_number' => '080314ff0109003',
                'specs' => null,
                'status' => 'good',
                'room' => 'Server Room',
                'parts' => [],
            ],
            [
                // Note: source left this device with no name, serial number, or
                // inventory number at all — placeholder name used. Fill in real
                // identifying info once the physical unit can be checked.
                'name' => 'Server Room Desktop (Unlabeled)',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => null,
                'inventory_number' => null,
                'specs' => null,
                'status' => 'good',
                'room' => 'Server Room',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP ProDisplay P232', 'inventory_number' => '080116it0102336', 'serial_number' => null],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '1308it0103054', 'serial_number' => 'dkusb1p02d31603a46k701'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '080116it0104336', 'serial_number' => null],
                ],
            ],
            [
                'name' => 'Server Room Aircon',
                'type' => 'aircon',
                'model_num' => 'Carrier',
                'serial_number' => null,
                'inventory_number' => '06acw2521',
                'specs' => null,
                'status' => 'good',
                'room' => 'Server Room',
                'parts' => [],
            ],
            [
                'name' => 'Fandialan',
                'type' => 'desktop',
                'model_num' => null,
                'serial_number' => 'bce8312hfd',
                'inventory_number' => '081018it0101501',
                'specs' => null,
                'status' => 'good',
                'room' => 'Server Room',
                'parts' => [
                    ['name' => 'Monitor', 'model_num' => 'HP V243', 'inventory_number' => '081018it0102501', 'serial_number' => 'cnk8210bxj'],
                    ['name' => 'Keyboard', 'model_num' => null, 'inventory_number' => '080318IT0103432', 'serial_number' => 'BCYRUOBSY98CMO'],
                    ['name' => 'Mouse', 'model_num' => null, 'inventory_number' => '081018IT0104499', 'serial_number' => 'CKW8311C9Q'],
                    ['name' => 'UPS', 'model_num' => null, 'inventory_number' => 'd320230202142', 'serial_number' => '320063bv0000223'],
                ],
            ],

            // ── Cashier ───────────────────────────────────────────

            [
                // Asset tag lists accountable person as "GOTIS, MAYLENE" —
                // matches the existing "Maylene R. Gotis (Cashier PC)" device.
                // Kept as its own standalone device rather than a sub-part,
                // since a printer isn't part of the desktop unit itself.
                'name' => 'Dot Matrix Printer (Maylene Gotis Desk)',
                'type' => 'printer',
                'model_num' => 'Epson LX-310, Dot Matrix 80 columns (Model PA71A)',
                'serial_number' => 'Q7CYJ36777',
                'inventory_number' => 'D5-2025-01-04-011',
                'specs' => null,
                'status' => 'good',
                'room' => 'Cashier',
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

            $query = Device::query();
            if (!empty($d['inventory_number'])) {
                $query->where('inventory_number', $d['inventory_number']);
            } elseif (!empty($d['serial_number'])) {
                $query->where('serial_number', $d['serial_number']);
            } else {
                $query->where('name', $d['name'])->where('room_id', $roomId);
            }

            $device = $query->first();

            if (!$device) {
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
}

