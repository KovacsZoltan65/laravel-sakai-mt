<?php

use App\Models\Tenant;
use App\Services\SettingsService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

if (!function_exists('is_hq')) {
    /**
     * Ellenőrzi, hogy a jelenlegi domain a HQ domain-e.
     *
     * @return bool
     */
    function is_hq(): bool
    {
        $host = request()->getHost(); // pl. hq.mt vagy tenant1.mt
        return $host === config('multitenancy.hq_domain', 'hq.mt');
    }
}

if (!function_exists('is_tenant')) {
    /**
     * Ellenőrzi, hogy tenant domain-en vagyunk-e.
     */
    function is_tenant(): bool
    {
        return !is_hq();
    }
}

if (!function_exists('tenant')) {
    /**
     * Visszaadja az aktuális tenant példányt.
     *
     * @return Tenant|null
     */
    function tenant(): ?Tenant
    {
        return SpatieTenant::current();
    }
}

if (!function_exists('selected_company')) {
    /**
     * Visszaadja a kiválasztott cég azonosítóját a session-ből.
     */
    function selected_company(): ?int
    {
        return session('selected_company_id');
    }
}

if (!function_exists('user')) {
    /**
     * Rövidítés az aktuális bejelentkezett felhasználóra.
     */
    function user(): ?Authenticatable
    {
        return Auth::user();
    }
}

/**
 * ========================================
 * HASZNÁLAT
 * ========================================
 * settings('locale');                 // pl. "hu"
 * settings('timezone');               // pl. "Europe/Budapest"
 * settings('unknown_key', 'default'); // fallback értékkel
 */
if( !function_exists('settings') ) {
    function settings(string $key, $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}