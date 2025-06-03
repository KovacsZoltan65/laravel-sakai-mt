<?php

namespace App\MultiTenancy\Tasks;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class CustomSwitchTenantDatabaseTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        if (!$tenant->active) {
            throw new \Exception("A tenant inaktív: {$tenant->id}");
        }

        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => $tenant->host ?? '127.0.0.1',
            'port' => $tenant->port ?? 3306,
            'database' => $tenant->database,
            'username' => $tenant->username,
            'password' => $tenant->password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    public function forgetCurrent(): void
    {
        DB::purge('tenant');
    }
}