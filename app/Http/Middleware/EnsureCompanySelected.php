<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Védelem: csak akkor vizsgáljuk, ha a session már elérhető
        if ($request->hasSession()) {
            if (!is_hq() && !$request->session()->has('selected_company_id')) {
                return redirect()->route('company.selector');
            }
        }

        return $next($request);
    }
}
