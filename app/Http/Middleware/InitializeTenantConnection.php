<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        
        /*
        // HQ domain kihagyása
        if ($request->getHost() === 'hq.tenant') {
            return $next($request);
        }
        
        // Tenant kikeresése domain alapján (landlord adatbázisból)
        $tenant = Tenant::where('domain', $request->getHost())->firstOrFail();
        */
        return $next($request);
    }
}
