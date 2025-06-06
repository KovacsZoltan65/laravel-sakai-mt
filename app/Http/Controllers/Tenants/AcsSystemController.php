<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcsSystems\AcsSystemStoreRequest;
use App\Http\Requests\AcsSystems\AcsSystemUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\AcsSystems\AcsSystemIndexRequest;
use App\Models\AcsSystem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            $_companies->orderBy($request->field, $request->order);
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
            
            return response()->json($company, Response::HTTP_OK);
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
        //
    }

    public function updateAcsSystem(AcsSystemUpdateRequest $request, int $id): JsonResponse
    {
        //
    }

    public function deleteAcsSystem(Request $request): JsonResponse
    {
        //
    }

    public function restoreAcsSystem(Request $request): JsonResponse
    {
        //
    }

    public function realDeleteAcsSystem(Request $request): JsonResponse
    {
        //
    }

    private function createDefaultSettings(Company $entity): JsonResponse
    {
        //
    }

    private function updateDefaultSettings(Company $centity): JsonResponse
    {
        //
    }
}
