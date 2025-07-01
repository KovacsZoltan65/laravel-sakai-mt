<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:migrate-one company_mt_03         | Lefuttatja a tenant migrációt
 * php artisan tenant:migrate-one company_mt_03 --fresh | Friss migráció (drop + újra)
 * php artisan tenant:migrate-one company_mt_03 --seed  | Migráció + seed
 * php artisan tenant:migrate-one company_mt_03 --fresh --seed
 * php artisan tenant:migrate-one 4 --fresh --seed      | ID alapján, frissen + seedelve
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class MigrateSingleTenant extends Command
{
    protected $signature = 'tenant:migrate-one {tenant} {--fresh} {--seed}';
    protected $description = 'Futtatja a migrációt egy adott tenant adatbázisra (opcionálisan frissen és seedelve)';

    public function handle(): int
    {
        $identifier = $this->argument('tenant');
        $isFresh = $this->option('fresh');
        $withSeed = $this->option('seed');

        // Tenant keresése ID, name vagy database alapján
        $tenant = Tenant::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('database', $identifier)
            ->first();

        if (!$tenant) {
            $this->error("❌ Tenant nem található: {$identifier}");
            return self::FAILURE;
        }

        $tenant->makeCurrent(); // ✅ Tenant aktiválása

        $this->info("📦 Migráció fut a tenant számára: {$tenant->name} [DB: {$tenant->database}]");

        // Friss vagy normál migráció
        $command = $isFresh ? 'migrate:fresh' : 'migrate';

        Artisan::call($command, [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->line(Artisan::output());

        // Opcionális seed
        if ($withSeed) {
            $this->info('🌱 Seed futtatása...');

            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--class' => 'DatabaseSeeder',
                '--force' => true,
            ]);

            $this->line(Artisan::output());
        }

        $tenant->forgetCurrent(); // ✅ Tenant leválasztása

        $this->info('✅ Tenant migráció sikeresen lefutott.');
        return self::SUCCESS;
    }
}
