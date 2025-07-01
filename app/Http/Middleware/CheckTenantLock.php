<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckTenantLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = Tenant::current();

        if ($tenant && $tenant->locked) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'A rendszer karbantartás alatt áll.'], 503);
            }

            //return response()->view('maintenance.tenant_locked', [
            //    'tenant' => $tenant,
            //], 503);
            //return response()->view('errors.503', [], 503);
            return Inertia::render('Errors/Maintenance');
        }
        
        return $next($request);
    }
}
