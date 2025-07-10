<?php

namespace App\Http\Controllers\Tenants\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class UserSettingsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/UserSettings/Index', [
            'title' => 'Felhasználói beállítások',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }
    
    public function fetch(Request $request)
    {
        $page = $request->input('page');
        
        try {
            $userId = user()?->id;

            if (!$userId) {
                return response()->json(['message' => 'Nincs bejelentkezett felhasználó!'], Response::HTTP_FORBIDDEN);
            }
        
            $_settings = UserSetting::query()
                ->where('user_id', $userId); // 🛡️ csak saját beállítás;
            
            if( $request->has('search') ) {
                $_settings->whereRaw("CONCAT(key,' ',value) LIKE '%{$request->get('search')}%'");
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
