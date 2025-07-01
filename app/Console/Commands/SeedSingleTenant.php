<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:seed-one company_mt_03            | tenant név
 * php artisan tenant:seed-one 5 --class=DatabaseSeeder | 5 = tenant id
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class SeedSingleTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-one {tenant} {--class=DatabaseSeeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seedelés egy adott tenant adatbázisban (alapértelmezetten DatabaseSeeder)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('tenant');
        $seederClass = $this->option('class');

        $tenant = Tenant::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('database', $identifier)
            ->first();
        
        if (!$tenant) {
            $this->error("❌ Tenant nem található: {$identifier}");
            return self::FAILURE;
        }
        
        $tenant->makeCurrent();
        
        $this->info("🌱 Seed futtatása tenantra: {$tenant->name} [DB: {$tenant->database}]");
        $this->info("🧬 Seeder: {$seederClass}");
        
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => $seederClass,
            '--force' => true,
        ]);
        
        $this->line(Artisan::output());
        
        $tenant->forgetCurrent();
        
        $this->info('✅ Seed sikeresen lefutott.');
        return self::SUCCESS;
    }
}
