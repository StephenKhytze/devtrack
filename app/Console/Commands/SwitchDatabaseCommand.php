<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SwitchDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:switch 
                            {target? : The target database mode ("actual", "demo", or "status")} 
                            {--migrate : Automatically run migrations and seed on switch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch active database between actual internal data (devtrack) and mock/demo data (devtrack_demo)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $currentDb = env('DB_DATABASE', config('database.connections.mysql.database', 'devtrack'));
        $target = strtolower($this->argument('target') ?? '');

        if ($target === 'status' || empty($target)) {
            $this->info("=========================================");
            $this->info("  DevTrack Database Environment Status   ");
            $this->info("=========================================");
            $isDemo = str_contains($currentDb, 'demo') || str_contains($currentDb, 'mock');
            
            $this->line("  Active Database : <fg=yellow>{$currentDb}</>");
            $this->line("  Mode            : " . ($isDemo ? '<fg=cyan>Mock / Demo Dataset (Fictional)</>' : '<fg=green>Actual Internal Dataset</>'));
            $this->newLine();
            $this->line("  To switch mode, run:");
            $this->line("    <fg=cyan>php artisan db:switch demo</>   -> Switch to mock data database");
            $this->line("    <fg=green>php artisan db:switch actual</> -> Switch to actual data database");
            $this->info("=========================================");
            return 0;
        }

        $targetDbName = match ($target) {
            'demo', 'mock'     => 'devtrack_demo',
            'actual', 'prod', 'real' => 'devtrack',
            default => null,
        };

        if (!$targetDbName) {
            $this->error("Invalid database target '{$target}'. Please specify 'actual' or 'demo'.");
            return 1;
        }

        if ($currentDb === $targetDbName) {
            $this->warn("Already connected to '{$targetDbName}'.");
        } else {
            $this->updateEnvDatabase($targetDbName);
            $this->info("✓ Updated .env [DB_DATABASE={$targetDbName}]");
            $this->call('config:clear');
        }

        // Check if MySQL is accessible and offer/run automatic migration if needed
        if ($this->option('migrate')) {
            $this->info("Running migrations and seeds for {$targetDbName}...");
            $this->call('migrate', ['--force' => true]);
        }

        $modeLabel = ($targetDbName === 'devtrack_demo') 
            ? '<fg=cyan>Mock / Demo Data (devtrack_demo)</>' 
            : '<fg=green>Actual Internal Data (devtrack)</>';

        $this->newLine();
        $this->info("Successfully switched active database to: {$modeLabel}");
        $this->line("Tip: You can verify your active database anytime with: <fg=yellow>php artisan db:switch status</>");

        return 0;
    }

    /**
     * Update DB_DATABASE in .env file.
     */
    private function updateEnvDatabase(string $newDatabaseName): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error(".env file not found at {$envPath}");
            return;
        }

        $envContent = File::get($envPath);

        if (preg_match('/^DB_DATABASE=.*$/m', $envContent)) {
            $envContent = preg_replace('/^DB_DATABASE=.*$/m', "DB_DATABASE={$newDatabaseName}", $envContent);
        } else {
            $envContent .= "\nDB_DATABASE={$newDatabaseName}\n";
        }

        File::put($envPath, $envContent);
    }
}
