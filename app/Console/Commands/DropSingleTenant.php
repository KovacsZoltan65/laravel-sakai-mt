<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:drop-one company_mt_03
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DropSingleTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:drop-one {tenant} {--delete-record}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Törli a tenant adatbázisát és felhasználóját (opcionálisan a rekordot is)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('tenant');
        $deleteRecord = $this->option('delete-record');
        
        // Tenant betöltése
        $tenant = Tenant::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('database', $identifier)
            ->first();
        
        if (!$tenant) {
            $this->error("❌ Tenant nem található: {$identifier}");
            return self::FAILURE;
        }
        
        $username = $tenant->username;
        $database = $tenant->database;
        $host = $tenant->host ?? 'localhost';
        
        // Megerősítés
        if (!$this->confirm("Biztosan törölni szeretnéd a(z) `$database` adatbázist és a hozzá tartozó `$username@$host` felhasználót?")) {
            $this->info('❎ Művelet megszakítva.');
            return self::SUCCESS;
        }
        
        $this->warn("🧨 Adatbázis és felhasználó törlése indul...");
        
        try {
            DB::connection('landlord')->statement("DROP DATABASE IF EXISTS `$database`");
            DB::connection('landlord')->statement("DROP USER IF EXISTS '$username'@'$host'");

            $this->info("✅ `$database` és `$username@$host` törölve.");

            if ($deleteRecord) {
                $tenant->delete();
                $this->info("🗑 Tenant rekord is törölve az adatbázisból.");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("💥 Hiba történt: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
