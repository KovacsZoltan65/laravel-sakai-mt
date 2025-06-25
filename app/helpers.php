<?php

use Illuminate\Support\Facades\Request;
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
     * @return \App\Models\Tenant|null
     */
    function tenant(): ?\App\Models\Tenant
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
    function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return Auth::user();
    }
}