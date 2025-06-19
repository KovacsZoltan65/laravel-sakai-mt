<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Hq/Dashboard', [
            'title' => 'Admin Dashboard',
            'users' => 10,
            'roles' => 0,
            'permissions' => 0,
            'companies' => 0,
            'tenants' => 0,
        ]);
    }

    public function employees(Request $request)
    {
        //
    }

    public function fetchEmployees(Request $request)
    {
        //
    }
}
