<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use \Exception;

class AppSettingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('AppSettings/Index', 
            [
                'title' => 'App Settings',
                'filters' => $request->all(['search', 'field', 'order']),
            ]
        );
    }
    
    public function fetch(Request $request)
    {
        $page = $request->input('page', 1);
        
        $settings = null;
        
        try {
            $_settings = AppSetting::query();
            
            if( $request->has(key: 'search') ) {
                $_settings->whereRaw("CONCAT(`key`,' ',`value`, ' ', `type`) LIKE '%{$request->get('search')}%'");
            }
            
            if( $request->has('field') && $request->has('order') ) {
                $_settings->orderBy($request->get('field'), $request->get('order'));
            }
            
            $settings = $_settings->paginate(10, ['*'], 'page', $page);
            
            return response()->json($settings);
            
        } catch( Exception $ex ) {
            \Log::info('$ex message: ' . print_r($ex->getMessage(), true));
            
            return response()->json(['AppSettings fetch error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
