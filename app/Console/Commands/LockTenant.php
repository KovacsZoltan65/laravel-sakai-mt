<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:lock company_mt_04
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class LockTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:lock {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lezárja (lockolja) a megadott tenantot karbantartáshoz, módosításhoz';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifier = $this->argument('tenant');
        
        $tenant = Tenant::where('id', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('database', $identifier)
            ->first();
        
        if (!$tenant) {
            $this->error("❌ Tenant nem található: {$identifier}");
            return self::FAILURE;
        }
        
        // 🧩 Ehhez kell egy új mező a tenants táblában:
        // ALTER TABLE tenants ADD COLUMN `locked` TINYINT(1) DEFAULT 0;
        
        $tenant->locked = true;
        $tenant->save();
        
        $this->info("🔒 Tenant zárolva: {$tenant->name} [ID: {$tenant->id}]");
        return self::SUCCESS;
        
    }
}
