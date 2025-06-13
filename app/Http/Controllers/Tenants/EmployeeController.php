<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Tenants\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        return Inertia::render('Tenants/Employee/Index', [
            'title' => 'Tenant Dashboard'
        ]);
        //if ($tenant = \App\Models\Tenant::current()) {
        //    echo "Aktív tenant: {$tenant->name}";
        //} else {
        //    echo "Nincs aktív tenant.";
        //}

        //dd("Employee Controller {$tenant->name}");
        //$employees = Employee::all();

        //dd($employees->toArray());
    }
}