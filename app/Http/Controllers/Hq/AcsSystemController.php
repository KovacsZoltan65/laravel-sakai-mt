<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcsSystem\AcsSystem\StoreRequest;
use App\Http\Requests\AcsSystem\AcsSystem\UpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\AcsSystem\AcsSystemIndexRequest;
use App\Models\AcsSystem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Response;

class AcsSystemController extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function index(Request $request): InertiaResponse
    {
        $tenants = \App\Models\Tenant::where('active', 1);
        return Inertia::render('Hq/AcsSystem/Index', 
            [
                'title' => 'Hq Acs Systems',
                'filters' => $request->all(['search', 'field', 'order']),
                'tenants' => $tenants,
            ]
        );
    }
    
    public function fetch(Request $request): JsonResponse
    {
        $tenant_id = $request->tenant_id;
        $tenant = \App\Models\Tenant::findOrFail($tenant_id);
        
        $acs_systems = null;
        
        try {
            $connectionName = app(\App\MultiTenancy\Tasks\CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);
            $_acs_systems = AcsSystem::on($connectionName);
            
            if( $request->has(key: 'search') ) {
                $_acs_systems->whereRaw("CONCAT(name) LIKE '%{$request->search}%'");
            }
            
            if($request->has('field') && $request->has('order')) {
                $_employees->orderBy($request->field, $request->order);
            }
            
            $acs_systems = $_acs_systems->paginate(10, ['*'], 'page', $request->page ?? 1);
            
            return response()->json($acs_systems, Response::HTTP_OK);
            
        } catch( \Exception $ex ) {
            \Log::info('error message: ' . print_r($ex->getMessage(), true));
        }
    }
    
    public function storeAcsSystem(AcsSystemStoreRequest $request){}
    
    public function updateAcsSystem(AcsSystemUpdateRequest $request, int $id){}
    
    //public function deleteAcsSystems(Request $request){}
    
    public function deleteAcsSystem(){}
    
    public function restoreAcsSystem(Request $request){}
    
    public function realDeleteAcsSystem(Request $request){}
    
    private function createDefaultSettings(AcsSystem $entity): JsonResponse
    {
        //
    }

    private function updateDefaultSettings(AcsSystem $centity): JsonResponse
    {
        //
    }
}