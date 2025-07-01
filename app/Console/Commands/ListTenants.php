<?php

/**
 * ============================================
 * HASZNÁLAT
 * ============================================
 * php artisan tenant:list
 */

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ListTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kilistázza az összes tenantot névvel, domainnel és adatbázisnévvel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::select(['id', 'name', 'domain', 'database', 'active'])
            ->orderBy('name')
            ->get();
        
        if ($tenants->isEmpty()) {
            $this->warn('⚠️  Nincs egyetlen tenant sem a rendszerben.');
            return self::SUCCESS;
        }
        
        $this->table(
            ['ID', 'Név', 'Domain', 'Adatbázis', 'Aktív'],
            $tenants->map(fn ($t) => [
                $t->id,
                $t->name,
                $t->domain,
                $t->database,
                $t->active ? '✅' : '❌',
            ])
        );
        
        $this->info('✅ Tenant lista megjelenítve.');
        return self::SUCCESS;
    }
}
