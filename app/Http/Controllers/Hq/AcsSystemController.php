<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcsSystem\AcsSystem\StoreRequest;
use App\Http\Requests\AcsSystem\AcsSystem\UpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
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