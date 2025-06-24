<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteEmployeeRequest;
use App\Http\Requests\IndexEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Tenants\Employee;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use \Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\MultiTenancy\Tasks\CustomSwitchTenantDatabaseTask;

class EmployeeController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        //$tenants = \App\Models\Tenant::where('active', 1)->where('name', '<>', 'Hq')->get(['id', 'name', 'database']);

        return Inertia::render('Hq/Employee/Index', [
            'title' => 'Employees',
            'filters' => $request->all(['search', 'field', 'order']),
            //'tenants' => $tenants,
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $tenant_id = $request->get('tenant_id');

        $tenant = Tenant::findOrFail($tenant_id);

        //$employees = null;

        $page = $request->input('page', 1);

        try {
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $_employees = Employee::on($connectionName);

            if( $request->has('company_id') ) {
                $_employees->where('company_id', '=', $request->get('company_id'));
            }

            if( $request->has(key: 'search') ) {
                $_employees->whereRaw("CONCAT(name,'',email)");
            }

            if ($request->has('field') && $request->has('order')) {
                $_employees->orderBy($request->get('field'), $request->get('order'));
            }

            $employees = $_employees->paginate(10, ['*'], 'page', $page);

            return response()->json([
                'employees' => $employees
            ], Response::HTTP_OK);
        } catch( Exception $ex ) {
            \Log::info('error message: ' . print_r($ex->getMessage(), true));
            return response()->json([
                'EmployeeController error' => $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $tenant_id = $request->get('tenant_id');
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $employee = DB::transaction(function() use($request, $connectionName): Employee {
                // 1. Employee létrehozása
                $_employee = Employee::on($connectionName)->create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_employee);

                // 3. Cache törlése, ha releváns

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'storeEmployee ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'storeEmployee QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'storeEmployee Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeEmployee Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateEmployee(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $tenant_id = $request->tenant_id;
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $employee = DB::transaction(function() use($request, $id, $connectionName): Employee {
                // 1. Módosítandó rekord zárolása és lekérése
                $_employee = Employee::on($connectionName)->lockForUpdate()->findOrFail($id);
                // 2. Rekord frissítése
                $_employee->update($request->all());
                // 3. Model frissítése
                $_employee->refresh();
                // 4. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_employee);

                // 5. Cache törlése, ha releváns

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'updateEmployee ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'updateEmployee QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'updateEmployee Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEmployees(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:employees,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];

            $tenant_id = $request->get('tenant_id');
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $deletedCount = DB::transaction(function() use($connectionName, $ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Employee::on($connectionName)->lockForUpdate()->whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$_cities = City::whereIn('id', $ids)->lockForUpdate()->get();
                //$_cities->each(function (City $city) use (&$deletedCount) {
                //    if ($city->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'deleteEmployees ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployees Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'deleteEmployees QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployees Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'deleteEmployees Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployees Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEmployee(Request $request, int $id): JsonResponse
    {
//\Log::info('$tenant_id: ' . print_r($request->tenant_id, true));
//\Log::info('$id: ' . print_r($id, true));
        try {

            $tenant_id = $request->tenant_id;
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $employee = DB::transaction(function() use($request, $connectionName, $id) {
                $_employee = Employee::on($connectionName)->lockForUpdate()->findOrFail($id);
                $_employee->delete();

                // Cache törlése, ha szükséges

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'deleteEmployee ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'deleteEmployee QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'deleteEmployee Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreEmployee(Request $request): JsonResponse
    {
        try {
            $tenant_id = $request->get('tenant_id');
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $employee = DB::transaction(function() use($connectionName, $request) {
                $_employee = Employee::on($connectionName)->lockForUpdate()->withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_employee->restore();

                // Friss adat betöltése
                $_employee->refresh();

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'restoreEmployee ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'restoreEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'restoreEmployee QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'restoreEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'restoreEmployee Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'restoreEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteEmployee(Request $request): JsonResponse
    {
        try {
            $tenant_id = $request->get('tenant_id');
            $tenant = Tenant::findOrFail($tenant_id);
            $connectionName = app(CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $employee = DB::transaction(function() use($connectionName, $request): Employee {
                // 1. Ország keresése
                $_employee = Employee::withTrashed()->on($connectionName)->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_employee->forceDelete();

                return $_employee;
            });

            return response()->json($employee,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'realDeleteEmployee ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'realDeleteEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'realDeleteEmployee QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'realDeleteEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'realDeleteEmployee Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'realDeleteEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createDefaultSettings(Employee $employee): void
    {
        //
    }

    private function updateDefaultSettings(Employee $employee): void
    {
        //
    }
}
