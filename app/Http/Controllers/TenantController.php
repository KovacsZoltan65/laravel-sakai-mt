<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        return Inertia::render('Tenants/Dashboard', [
            'title' => 'Tenant Dashboard'
        ]);
    }
}