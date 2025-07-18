<?php

namespace App\Http\Controllers\Tenants\Settings;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function index()
    {
        return AppSetting::all();
    }

    public function resolve(Request $request, string $key)
    {
        $userId = $request->user()?->id;
        $companyId = session('selected_company_id'); // vagy ahol tárolod

        $resolved = SettingsService::resolve($key, $userId, $companyId);

        return response()->json($resolved);
    }
    
    public function store(Request $request)
    {
        return AppSetting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value, 'type' => $request->type]
        );
    }

    public function destroy($key)
    {
        return AppSetting::where('key', $key)->delete();
    }
}
