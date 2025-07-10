<?php

namespace App\Http\Controllers\Tenants\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Exception;

class SettingsPanelController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/SettingsPanel', [
            'title' => 'Beállítások áttekintő',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }
    
    public function fetch(Request $request)
    {
        //
    }
}
