<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * 
 * | Paraméter  | Kötelező | Típus  | Példa érték            | Magyarázat                                                                                                                                |
 * | ---------- | -------- | ------ | ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
 * | `name`     | ✅ Igen   | string | `"Company 04"`         | A tenant megjelenített neve (pl. cég neve). Ez kerül a `name` mezőbe a `tenants` táblában.                                                |
 * | `domain`   | ✅ Igen   | string | `company04.local`      | A tenant domainje (pl. helyi vagy éles domain, pl. `tenant01.example.com`).                                                               |
 * | `database` | ✅ Igen   | string | `company_mt_04`        | A MySQL adatbázis neve, amely létrejön a tenantnak.                                                                                       |
 * | `username` | ✅ Igen   | string | `tenant_user_04`       | A MySQL felhasználó neve, amely a tenant adatbázisához kap jogokat.                                                                       |
 * | `password` | ✅ Igen   | string | `Pa$$w0rd`             | A MySQL felhasználó jelszava.                                                                                                             |
 * | `--host`   | ❌ Nem    | string | `localhost` vagy `%`   | A MySQL host. `localhost` = csak lokális elérés, `%` = bármilyen IP (pl. Docker vagy másik szerver esetén). Alapértelmezett: `localhost`. |
 * | `--active` | ❌ Nem    | 0 / 1  | `1`                    | Aktív legyen-e a tenant. `1` = aktív, `0` = inaktív. Ez megy az `active` mezőbe. Alapértelmezett: `1`.                                    |
 * | `--seeder` | ❌ Nem    | string | `TenantDatabaseSeeder` | Melyik seeder osztály fusson le a migráció után. Alapértelmezett: `DatabaseSeeder`.                                                       |
 * 
 * php artisan tenant:setup "Company 04" company04.local company_mt_04 tenant_user_04 Pa$$w0rd --host=% --active=1 --seeder=TenantDatabaseSeeder
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class SetupTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup
        {name : A tenant megnevezése}
        {domain : A tenant domain neve}
        {database : Az adatbázis neve}
        {username : A MySQL felhasználó neve}
        {password : A MySQL felhasználó jelszava}
        {--host=localhost : MySQL hoszt (alapértelmezés: localhost)}
        {--active=1 : Aktív legyen-e a tenant (1/0)}
        {--seeder=DatabaseSeeder : Futó seeder osztály}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teljes tenant létrehozás (adatbázis, felhasználó, migráció, seed)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $database = $this->argument('database');
        $username = $this->argument('username');
        $password = $this->argument('password');
        $host = $this->option('host');
        $active = (bool) $this->option('active');
        $seederClass = $this->option('seeder');
        
        // 1️⃣ Rekord létrehozása
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
        
        // 2️⃣ Adatbázis és user létrehozása
        $this->info("🛠 Adatbázis és felhasználó létrehozása...");
        
        DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci");
        DB::connection('landlord')->statement("CREATE USER IF NOT EXISTS '$username'@'$host' IDENTIFIED WITH sha256_password BY '$password'");
        DB::connection('landlord')->statement("GRANT USAGE ON *.* TO '$username'@'$host'");
        DB::connection('landlord')->statement("GRANT ALL PRIVILEGES ON `$database`.* TO '$username'@'$host'");
        DB::connection('landlord')->statement("FLUSH PRIVILEGES");
        
        $this->info("✅ Adatbázis és felhasználó létrehozva.");
        
        // 3️⃣ Tenant adatbázis inicializálás
        $tenant->makeCurrent();
        
        $this->info("📦 Migráció futtatása...");
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

        $this->info('✅ Tenant teljes setup sikeresen megtörtént.');
        return self::SUCCESS;
    }
}
