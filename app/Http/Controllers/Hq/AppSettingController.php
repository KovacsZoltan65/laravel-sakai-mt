<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppSettingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('AppSettings/Index', 
            [
                'title' => 'App Settings',
                'filters' => '',
            ]
        );
    }
}
