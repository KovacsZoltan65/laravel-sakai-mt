<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        
        $company = null;
        
        $page = $request->input('params.page', 1);
        
        try{
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);
            $_company = Company::on($connectionName);
            
            if( $request->has(key: 'search') ) {
                $_company->whereRaw("CONCAT(name,'',email)");
            }
            
            if ($request->has('field') && $request->has('order')) {
                $_company->orderBy($request->get('field'), $request->get('order'));
            }
            
            $company = $_company->paginate(10, ['*'], 'page', $page);
        } catch( Exception $ex ) {
            \Log::info('error message: ' . print_r($ex->getMessage(), true));
        }
    }
}
