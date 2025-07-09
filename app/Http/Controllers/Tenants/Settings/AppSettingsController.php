<?php

namespace App\Http\Controllers\Tenants\Settings;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class AppSettingsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/AppSettings/Index', [
            'title' => 'Cég beállítások'
        ]);
    }
    
    public function fetch(Request $request)
    {
        $page = $request->input('page');
        
        try {
            $_settings = AppSetting::query();
            
            if( $request->has('search') ) {
                $_settings->whereRaw("CONCAT(key,'',vale) LIKE '%{$request->get('search')}%'");
            }
            
            if( $request->has('field') && $request->has('order') ) {
                $_settings->orderBy($request->get('field'), $request->get('order'));
            }
            
            $settings = $_settings->paginate(10, ['*'], 'page', $page);
            
            return response()->json($settings, Response::HTTP_OK);
            
        } catch( Exception $ex ) {
            \Log::info('$ex message: ' . print_r($ex->getMessage(), true));
            return response()->json($settings, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
