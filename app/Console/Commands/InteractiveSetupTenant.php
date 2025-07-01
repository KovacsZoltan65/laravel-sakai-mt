<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:setup-interactive
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class InteractiveSetupTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interaktív tenant létrehozás: rekord, adatbázis, felhasználó, migráció, seed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🚀 Tenant létrehozási varázsló indul...");
        
        // 🧾 Adatok bekérése
        $name = $this->ask('Név (pl. Company Kft)');
        $domain = $this->ask('Domain (pl. company.local)');
        $database = $this->ask('Adatbázis neve (pl. company_mt_04)');
        $username = $this->ask('MySQL felhasználó neve (pl. tenant_user_04)');
        $password = $this->secret('MySQL jelszó');
        $host = $this->ask('MySQL hoszt (alapértelmezés: localhost)', 'localhost');
        $active = $this->confirm('Aktív tenant legyen?', true) ? 1 : 0;
        $seederClass = $this->ask('Seeder osztály (alapértelmezés: DatabaseSeeder)', 'DatabaseSeeder');
        
        // 🚧 Rekord létrehozása
        $tenant = Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'host' => $host,
            'port' => 3306,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'active' => $active,
        ]);
        
        $this->info("📋 Tenant rekord létrehozva: {$tenant->name} [{$tenant->database}]");
        
        // 🛠 Adatbázis és user létrehozása
        $this->warn("🧱 Adatbázis és MySQL felhasználó létrehozása...");
        
        DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci");
        DB::connection('landlord')->statement("CREATE USER IF NOT EXISTS '$username'@'$host' IDENTIFIED WITH sha256_password BY '$password'");
        DB::connection('landlord')->statement("GRANT USAGE ON *.* TO '$username'@'$host'");
        DB::connection('landlord')->statement("GRANT ALL PRIVILEGES ON `$database`.* TO '$username'@'$host'");
        DB::connection('landlord')->statement("FLUSH PRIVILEGES");
        
        $this->info("✅ Adatbázis és felhasználó létrehozva.");
        
        // ⚙️ Tenant inicializálása
        $tenant->makeCurrent();
        
        $this->info("📦 Migrációk futtatása...");
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
        
        $this->line(Artisan::output());
        
        $this->info("🌱 Seeder futtatása: {$seederClass}");
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => $seederClass,
            '--force' => true,
        ]);
        $this->line(Artisan::output());
        
        $tenant->forgetCurrent();
        
        $this->info('🎉 Tenant teljes setup sikeresen befejeződött.');
        return self::SUCCESS;
    }
}
