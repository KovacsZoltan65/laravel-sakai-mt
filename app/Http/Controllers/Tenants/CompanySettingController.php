<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanySettingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CompanySettings/Index', 
            [
                'title' => 'Company Settings',
                'filters' => '',
            ]
        );
    }
}
