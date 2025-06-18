<?php

namespace App\MultiTenancy\Tasks;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class CustomSwitchTenantDatabaseTask implements SwitchTenantTask
{
    public function switchToTenant(Tenant $tenant): string
    {
        $connectionName = 'tenant_' . $tenant->id;
        
        if( !Config::has("database.connections.{$connectionName}") ) {
            Config::set("database.connections.{$connectionName}", [
                'driver' => 'mysql',
                'host' => $tenant['host'],
                'port' => $tenant['port'],
                'database' => $tenant['database'],
                'username' => $tenant['username'],
                'password' => $tenant['password'],
                'charset' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => 'InnoDB',
            ]);
        }
        
        DB::purge($connectionName); // Előző kapcsolat törlése, ha volt
        DB::reconnect($connectionName); // Új kapcsolat létrehozása
        
        try {
            \DB::connection($connectionName)->getPdo();
        } catch( \Exception $ex ) {
            logger()->error('A kapcsolat nem jött létre: ' . $tenant['name']);
            throw new \Exception('Nem sikerült kapcsolódni a tenant adatbázishoz: ' . $tenant['name']);

        }
        
        return $connectionName;
    }
    
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