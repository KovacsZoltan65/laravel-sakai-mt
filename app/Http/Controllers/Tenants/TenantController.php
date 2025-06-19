<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        return Inertia::render('Tenants/Dashboard', [
            'title' => 'Tenant Dashboard'
        ]);
    }

    public function getTenantsToSelect(): array
    {
        // Get all tenants
        $tenants = Tenant::ToSelect();

        return $tenants;
    }
}
