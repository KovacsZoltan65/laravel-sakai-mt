<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:unlock company_mt_04
 */

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class UnlockTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:unlock {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Feloldja a tenant lock állapotát (karbantartás befejezése után)';

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
        
        $tenant->locked = false;
        $tenant->save();
        
        $this->info("🔓 Tenant feloldva: {$tenant->name}");
        return self::SUCCESS;
    }
}
