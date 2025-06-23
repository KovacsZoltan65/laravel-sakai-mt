<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant;
use App\MultiTenancy\Tasks\CustomSwitchTenantDatabaseTask;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        return Inertia::render('Hq/Companies/Index', [
            'title' => 'Companies',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $tenant_id = $request->get('tenant_id');

        $tenant = Tenant::findOrFail($tenant_id);

        $page = $request->input('params.page', 1);

        try{
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);
            $_companies = Company::on($connectionName);

            if( $request->has(key: 'search') ) {
                $_companies->whereRaw("CONCAT(name,'',email) LIKE '%{$request->get('search')}%'");
            }

            if ($request->has('field') && $request->has('order')) {
                $_companies->orderBy($request->get('field'), $request->get('order'));
            }

            $companies = $_companies->paginate(10, ['*'], 'page', $page);

            return response()->json(['companies' => $companies], Response::HTTP_OK);
        } catch( Exception $ex ) {
            \Log::info('error message: ' . print_r($ex->getMessage(), true));
            return response()->json(['CompanyController error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
