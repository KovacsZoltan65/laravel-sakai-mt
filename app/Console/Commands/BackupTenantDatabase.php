<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * Mentés helye: storage/app/backups/tenants/company_mt_01/backup_20240701_123500.sql(.gz)
 * php artisan tenant:backup company_mt_01        | Mentés sima .sql formátumban.
 * php artisan tenant:backup company_mt_01 --gzip | Mentés .sql.gz formátumban (tömörített)
 */

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:backup {tenant} {--gzip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mentést készít a megadott tenant adatbázisáról (mysqldump)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('tenant');
        $gzip = $this->option('gzip');
        
        $tenant = Tenant::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('database', $identifier)
            ->first();
        
        if (!$tenant) {
            $this->error("❌ Tenant nem található: {$identifier}");
            return self::FAILURE;
        }
        
        $dbName = $tenant->database;
        $username = $tenant->username;
        $password = $tenant->password;
        $host = $tenant->host ?? 'localhost';
        
        $timestamp = now()->format('Ymd_His');
        $filename = "backup_{$timestamp}.sql" . ($gzip ? '.gz' : '');
        $directory = "backups/tenants/{$dbName}";
        $fullPath = storage_path("app/{$directory}/{$filename}");
        
        // 🗂️ Könyvtár létrehozása, ha nem létezik
        Storage::makeDirectory($directory);
        
        // 🧠 Dump parancs összeállítása
        $dumpCommand = "mysqldump -u {$username} -p'{$password}' -h {$host} {$dbName}";
        if ($gzip) {
            $dumpCommand .= " | gzip > \"{$fullPath}\"";
        } else {
            $dumpCommand .= " > \"{$fullPath}\"";
        }
        
        $this->info("💾 Mentés fut: {$filename}");
        
        exec($dumpCommand, $output, $result);
        
        if ($result !== 0) {
            $this->error("💥 Hiba a mentés során. Ellenőrizd a jogosultságokat vagy az elérhetőséget.");
            return self::FAILURE;
        }
        
        $this->info("✅ Mentés sikeres: storage/app/{$directory}/{$filename}");
        return self::SUCCESS;
    }
}
