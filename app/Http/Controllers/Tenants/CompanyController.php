<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyIndexRequest;
use App\Models\Company;
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
        return Inertia::render('Tenants/Companies/Index', [
            'title' => 'Companies',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(CompanyIndexRequest $request): JsonResponse
    {
        $page = $request->input('params.page', 1);

        $companies = null;

        try {
            $_companies = Company::query();

            if( $request->has('search') ) {
                $_companies->whereRaw("CONCAT(name) LIKE '%{$request->search}%'");
            }

            if( $request->has('field') && $request->has('order') ) {
                $_companies->orderBy($request->field, $request->order);
            }

            $companies = $_companies->paginate(10, ['*'], 'page', $page);

            return response()->json($companies, Response::HTTP_OK);

        } catch( Exception $ex ) {
            \Log::info('getMessage: ' . print_r($ex->getMessage(), true));
        }
    }
}
