<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class InitializeTenantConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost(); // pl. company01.tenant
        $tenant = Tenant::where('domain', $host)->firstOrFail();
        $tenant->makeCurrent(); // kapcsol váltás
        
        // 🔧 Laravel config újraállítása
        config([
            'database.connections.mysql.database' => $tenant->database,
            'database.connections.mysql.username' => $tenant->username,
            'database.connections.mysql.password' => $tenant->password,
        ]);

        // 🔄 kapcsolat újraépítése
        DB::purge('mysql');
        DB::reconnect('mysql');

        return $next($request);
    }
}
