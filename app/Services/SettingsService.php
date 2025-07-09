<?php

namespace App\Services;

use App\Models\UserSetting;
use App\Models\CompanySetting;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    /**
     * Beállítás lekérése a teljes öröklési lánc mentén.
     */
    public function get(string $key, $default = null): mixed
    {
        $user = Auth::user();
        $companyId = session('selected_company_id');

        // Cache kulcs
        $cacheKey = "settings.{$user?->id}.{$companyId}.{$key}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($key, $user, $companyId, $default) {
            // 1. UserSetting
            if ($user) {
                $setting = UserSetting::where('user_id', $user->id)->where('key', $key)->first();
                if ($setting) {
                    return $this->cast($setting->value, $setting->type);
                }
            }

            // 2. CompanySetting
            if ($companyId) {
                $setting = CompanySetting::where('company_id', $companyId)->where('key', $key)->first();
                if ($setting) {
                    return $this->cast($setting->value, $setting->type);
                }
            }

            // 3. AppSetting
            $setting = AppSetting::where('key', $key)->first();
            if ($setting) {
                return $this->cast($setting->value, $setting->type);
            }

            // Az alap értékkel tér vissza
            return $default;
        });
    }

    /**
     * Beállítás típuskényszerítése a tárolt típus alapján.
     */
    protected function cast(string $value, string $type): mixed
    {
        return match ($type) {
            'int' => (int) $value,
            'bool' => $value == '1' || strtolower($value) === 'true',
            'json' => json_decode($value, true),
            default => $value,
        };
    }
    
    public function allUserSettings(int $userId): array
    {
        //
    }
    
    public function has(string $key): bool
    {
        //
    }
}
