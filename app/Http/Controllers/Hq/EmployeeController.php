<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteEmployeeRequest;
use App\Http\Requests\IndexEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Tenants\Employee;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $tenants = \App\Models\Tenant::where('active', 1)
            ->where('name', '<>', 'Hq')
            ->get(['id', 'name', 'database']);

        return Inertia::render('Hq/Employee/Index', [
            'title' => 'Employees',
            'filters' => $request->all(['search', 'field', 'order']),
            'tenants' => $tenants,
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $tenant_id = $request->get('tenant_id');
        
        $tenant = \App\Models\Tenant::findOrFail($tenant_id);

        $employees = null;
        
        try {
            $connectionName = app(\App\MultiTenancy\Tasks\CustomSwitchTenantDatabaseTask::class)->switchToTenant($tenant);

            $_employees = Employee::on($connectionName);

            if( $request->has(key: 'search') ) {
                $_employees->whereRaw("CONCAT(name,'',email)");
            }

            if ($request->has('field') && $request->has('order')) {
                $_employees = $_employees;
            }

            $employees = $_employees->paginate(10, ['*'], 'page', $request->page ?? 1);
        } catch( \Exception $ex ) {
            \Log::info('error message: ' . print_r($ex->getMessage(), true));
        }
        
        return response()->json(['employees' => $employees]);
    }
    /*
    public function getEmployee(Request $request): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($request->id);

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getEmployee Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEmployeeByName(string $name): JsonResponse
    {
        try {
            $employee = Company::where('name', '=', $name)->firstOrFail();

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getEmployeeByName ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployeeByName Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getEmployeeByName QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployeeByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getEmployeeByName Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEmployeeByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeEmployee(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $employee = DB::transaction(function() use($request): Employee {
                // 1. Employee létrehozása
                $_employee = Employee::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_employee);

                // 3. Cache törlése, ha releváns


                return $_employee;
            });

            return response()->json($employee, Response::HTTP_CREATED);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'storeEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'storeEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'storeEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'storeEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'storeEmployee Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'storeEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateEmployee(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $employee = DB::transaction(function() use($request, $id) {
                // 1. Módosítandó rekord zárolása és lekérése
                $_employee = Entity::lockForUpdate()->findOrFail($id);

                // 2. Rekord frissítése
                $_employee->update($request->all());
                // 3. Model frissítése
                $_employee.refresh();

                // 4. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_employee);


                // 5. Cache törlése, ha releváns


                return $_employee;
            });

            return response()->json($employee, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'updateEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'updateEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'updateEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'updateEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'updateEmployee Exception: ' . print_r(value: $ex, return: true));
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

            $deletedCount = DB::transaction(function() use($ids) {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Employee::whereIn('id', $ids)->delete();

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
            \Log::info(message: 'deleteEmployees ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployees Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'deleteEmployees QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployees Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'deleteEmployees Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployees Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEmployee(DeleteEmployeeRequest $request): JsonResponse
    {
        try {
            $employee = DB::transaction(function() use($request): Company {
                $_employee = Company::lockForUpdate()->findOrFail($request->id);
                $_employee->delete();

                // Cache törlése, ha szükséges

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'deleteEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'deleteEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'deleteEmployee Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreEmployee(Request $request): JsonResponse
    {
        try {
            $employee = DB::transaction(function () use ($request): Company {
                // Soft-deleted ország lekérése
                $_employee = Company::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_employee->restore();

                // Friss adat betöltése
                $_employee->refresh();

                return $_employee;
            });

            return response()->json($employee, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'restoreEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'restoreEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'restoreEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'restoreEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'restoreEmployee Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'restoreEmployee Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteEmployee(Request $request): JsonResponse
    {
        try {
            $employee = DB::transaction(function()use($request): Company {
                // 1. Ország keresése
                $_employee = Company::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_employee->forceDelete();

                return $_employee;
            });

            return response()->json($employee,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'realDeleteEmployee ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'realDeleteEmployee Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'realDeleteEmployee QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'realDeleteEmployee Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'realDeleteEmployee Exception: ' . print_r(value: $ex, return: true));
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
    */
}
