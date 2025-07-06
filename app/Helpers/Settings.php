<?php

namespace App\Helpers;

class Settings
{
    public static function getApp($key, $default = null)
    {
        return App\Models\AppSetting::where('key', $key)->value('value') ?? $default;
    }
    
    public static function getCompany($key, $default = null)
    {
        return App\Models\CompanySetting::where('key', $key)->value('value') ?? $default;
    }
    
    public static function setApp($key, $value)
    {
        return App\Models\AppSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    public static function setCompany($key, $value)
    {
        return App\Models\CompanySetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
