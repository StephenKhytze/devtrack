<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceStatus;
use App\Models\MaintenanceLog;
use App\Models\User;
use Carbon\Carbon;

class MaintenanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $good        = DeviceStatus::where('label', 'Good')->first();
        $maintenance = DeviceStatus::where('label', 'Maintenance')->first();
        $oos         = DeviceStatus::where('label', 'Out of Service')->first();

        $performers = User::whereIn('username', ['admin', 'jdelacruz', 'mreyes'])->get();

        if ($performers->isEmpty()) {
            $this->command?->warn('MaintenanceLogSeeder: no matching users found — skipping.');
            return;
        }

        // ── Description pools by device type ────────────────────

        $descriptionsByType = [
            'desktop' => [
                ['text' => 'User reported the system freezing during multitasking. Diagnosed insufficient memory under load, upgraded RAM and reseated all modules. System stable after testing.', 'outcome' => 'good_to_good'],
                ['text' => 'Routine preventive maintenance. Cleaned internal dust buildup, verified all cable connections, updated OS and antivirus definitions. No issues found.', 'outcome' => 'good_to_good'],
                ['text' => 'System running noticeably slow. Ran disk cleanup, cleared temp files, checked startup programs. Performance improved to normal.', 'outcome' => 'good_to_good'],
                ['text' => 'Unit failed to boot. Checked power supply and RAM seating — found a loose RAM stick. Reseated and unit now boots normally.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Intermittent blue-screen crashes reported. Ran memory diagnostic, found no hardware fault. Updated drivers and monitored — flagged for follow-up.', 'outcome' => 'good_to_maintenance'],
            ],
            'laptop' => [
                ['text' => 'Battery draining unusually fast. Checked battery health and background processes, optimized power settings. Improved but scheduled for continued monitoring.', 'outcome' => 'good_to_maintenance'],
                ['text' => 'Routine checkup requested. Cleaned vents, updated OS and drivers, verified charging circuit. No issues found.', 'outcome' => 'good_to_good'],
                ['text' => 'Unit overheating during extended use. Cleaned internal fan and reapplied thermal paste. Temperatures back to normal range.', 'outcome' => 'maintenance_to_good'],
            ],
            'printer' => [
                ['text' => 'Printer jamming on every print job. Cleaned paper path and checked rollers — jam issue resolved, printing normally.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Print output showing streaking and uneven toner coverage. Cleaned drum unit and replaced toner cartridge. Quality improved but flagged for follow-up.', 'outcome' => 'good_to_maintenance'],
                ['text' => 'Low toner warning reported by staff. Replaced toner cartridge and ran test print to confirm output quality. Resolved.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Routine scheduled maintenance. Cleaned scanner glass and paper feed rollers, checked network connectivity. No issues found.', 'outcome' => 'good_to_good'],
                ['text' => 'Unit unresponsive over the network. Power-cycled device and reconfigured network settings — connection restored, functioning normally.', 'outcome' => 'good_to_good'],
                ['text' => 'Persistent paper jams despite cleaning. Inspected feed rollers, found visible wear. Unit taken out of service pending replacement part.', 'outcome' => 'good_to_oos'],
            ],
            'photocopier' => [
                ['text' => 'Copier producing faded output on large jobs. Replaced toner and cleaned drum assembly. Output quality restored.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Routine scheduled maintenance. Cleaned glass and rollers, checked paper feed alignment. No issues found.', 'outcome' => 'good_to_good'],
            ],
            'aircon' => [
                ['text' => 'Annual aircon maintenance. Cleaned filters, checked refrigerant levels, inspected condenser coils. Unit operating within normal parameters.', 'outcome' => 'good_to_good'],
                ['text' => 'Weak airflow and reduced cooling reported. Cleaned filters and coils, checked refrigerant charge — cooling restored to normal.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Unit making unusual noise during operation. Inspected fan motor and mounting — found loose mounting bracket, tightened and re-tested. Operating normally.', 'outcome' => 'good_to_good'],
            ],
            'telephone' => [
                ['text' => 'Static and dropped calls reported. Checked line connection and handset — reseated cable, issue resolved.', 'outcome' => 'good_to_good'],
                ['text' => 'Routine functionality check. Tested dial tone, ringer, and handset — no issues found.', 'outcome' => 'good_to_good'],
            ],
            'default' => [
                ['text' => 'Routine maintenance check performed. Inspected unit and connections — no issues found.', 'outcome' => 'good_to_good'],
                ['text' => 'Reported malfunction investigated. Issue identified and resolved after inspection and minor repair.', 'outcome' => 'maintenance_to_good'],
                ['text' => 'Performance issue reported. Inspected unit — minor issue found, flagged for continued monitoring.', 'outcome' => 'good_to_maintenance'],
            ],
        ];

        $outcomeStatuses = [
            'good_to_good'        => [$good, $good],
            'good_to_maintenance' => [$good, $maintenance],
            'maintenance_to_good' => [$maintenance, $good],
            'good_to_oos'         => [$good, $oos],
        ];

        // ── Build logs across three time buckets, oldest first ──
        // (processed oldest -> newest so each device's final status
        // ends up reflecting its most recent log, same as production)

        $buckets = [9, 6, 3]; // months ago
        $logsPerBucket = 5;
        $now = Carbon::now();

        foreach ($buckets as $monthsAgo) {
            $devices = Device::inRandomOrder()->take($logsPerBucket)->get();

            foreach ($devices as $device) {
                $pool = $descriptionsByType[$device->type] ?? $descriptionsByType['default'];
                $entry = $pool[array_rand($pool)];
                [$before, $after] = $outcomeStatuses[$entry['outcome']];
                $performer = $performers->random();
                $date = $now->copy()->subMonths($monthsAgo)->subDays(rand(0, 20));

                MaintenanceLog::create([
                    'device_id'        => $device->id,
                    'performed_by'     => $performer->id,
                    'date'             => $date->format('Y-m-d'),
                    'description'      => $entry['text'],
                    'status_before_id' => $before->id,
                    'status_after_id'  => $after->id,
                ]);

                $device->update(['status_id' => $after->id]);
            }
        }
    }
}
