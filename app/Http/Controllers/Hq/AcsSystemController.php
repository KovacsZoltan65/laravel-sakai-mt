<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Tenants\AcsSystemController AS TenantAcsSystem;
use App\Http\Requests\AcsSystems\AcsSystemStoreRequest;
use App\Http\Requests\AcsSystems\AcsSystemUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\AcsSystems\AcsSystemIndexRequest;
use App\Models\AcsSystem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Response;

class AcsSystemController extends TenantAcsSystem
{
    public function __construct()
    {
        //
    }
    
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('AcsSystems/Index', 
            [
                'title' => 'Hq Acs Systems',
                'filters' => $request->all(['search', 'field', 'order']),
            ]
        );
    }
    
    public function fetch(AcsSystemIndexRequest $request)
    {
        //
    }
}