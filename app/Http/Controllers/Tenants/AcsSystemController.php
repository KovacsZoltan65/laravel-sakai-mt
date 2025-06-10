<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcsSystems\AcsSystemStoreRequest;
use App\Http\Requests\AcsSystems\AcsSystemUpdateRequest;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\AcsSystems\AcsSystemIndexRequest;
use App\Models\AcsSystem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Response;

class AcsSystemController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Acs/Index', [
            'title' => 'Acs Systems',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(AcsSystemIndexRequest $request)
    {
        $_acs_systems = AcsSystem::query();

        if( $request->has(key: 'search') ) {
            $_acs_systems->whereRaw("CONCAT(name)");
        }

        if ($request->has('field') && $request->has('order')) {
            $_acs_systems->orderBy($request->field, $request->order);
        }

        $acs_systems = $_acs_systems->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($acs_systems, Response::HTTP_OK);
    }

    public function getAcsSystem(Request $request): JsonResponse
    {
        try {
            $acs = AcsSystem::findOrFail($request->id);
        } catch( ModelNotFoundException $e ) {
            return response()->json(['error' => 'getAcsSystem AcsSystem not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $e ) {
            return response()->json(['error' => 'getAcsSystem Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $e ) {
            return response()->json(['error' => 'getAcsSystem Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAcsSystemByName(string $name): JsonResponse
    {
        try {
            $acs = AcsSystem::where('name', '=', $name)->firstOrFail();
            
            return response()->json($acs, Response::HTTP_OK);
        } catch( ModelNotFoundException $e ) {
            return response()->json(['error' => 'getAcsSystemByName AcsSystem not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $e ) {
            return response()->json(['error' => 'getAcsSystemByName Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $e ) {
            return response()->json(['error' => 'getAcsSystemByName Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeAcsSystem(AcsSystemStoreRequest $request): JsonResponse
    {
        try {
            $acs = DB::transaction(function() use($request): AcsSystem {
                // 1. Entitás létrehozása
                $_acs = AcsSystem::create($request->all());
                
                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_acs);
                
                // 3. Cache törlése, ha releváns
                
                
                return $_acs;
            });
            
            return response()->json($acs, Response::HTTP_CREATED);
        } catch( QueryException $ex ) {
            return response()->json(['error' => 'storeAcsSystem Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            return response()->json(['error' => 'storeAcsSystem Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAcsSystem(AcsSystemUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $acs = DB::Transaction(function($request, $id): AcsSystem {
                // 1. Entitás módosítása
                $_acs = AcsSystem::lockForUpdate()->findOrFail($id);
                $_acs->update($request->all());
                $_acs->refresh();
                
                // 2. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_acs);
                
                // 3. Cache törlése, ha releváns
                
                
                return $_acs;
            });
            
            return response()->json($acs, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            return response()->json(['error' => 'updateAcsSystem AcsSystem not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            return response()->json(['error' => 'updateAcsSystem Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            return response()->json(['error' => 'updateAcsSystem Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAcsSystem(Request $request): JsonResponse
    {
        try {
            $acs = DB::transaction(function() use($request): AcsSystem {
                $_acs = AcsSystem::lockForUpdate()->findOrFail($request->id);
                $_acs->delete();

                // Cache törlése, ha szükséges

                return $_acs;
            });
            
            return response()->json($acs, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            return response()->json(['error' => 'deleteAcsSystem Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( QueryException $ex ) {
            return response()->json(['error' => 'deleteAcsSystem Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            return response()->json(['error' => 'deleteAcsSystem Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreAcsSystem(Request $request): JsonResponse
    {
        try {

            $acs = DB::transaction(function () use ($request): Company {
                // Soft-deleted ország lekérése
                $_acs = AcsSystem::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_acs->restore();

                // Friss adat betöltése
                $_acs->refresh();

                return $_acs;
            });

            return response()->json($acs, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreAcsSystem ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreAcsSystem City not found'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $ex) {
            \Log::error('restoreAcsSystem QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreAcsSystem Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $ex) {
            \Log::error('restoreAcsSystem Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreAcsSystem Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteAcsSystem(Request $request): JsonResponse
    {
        try {

            $acs = DB::transaction(function()use($request): AcsSystem {
                // 1. Ország keresése
                $_acs = AcsSystem::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_acs->forceDelete();

                return $_acs;
            });

            return response()->json($acs,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteAcsSystem ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteAcsSystem City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteAcsSystem QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteAcsSystem Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteAcsSystem Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteAcsSystem Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createDefaultSettings(AcsSystem $entity): JsonResponse
    {
        //
    }

    private function updateDefaultSettings(AcsSystem $centity): JsonResponse
    {
        //
    }
}
