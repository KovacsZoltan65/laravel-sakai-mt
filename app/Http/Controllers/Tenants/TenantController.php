<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantController extends Controller
{
    public function dashboard(Request $request)
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
