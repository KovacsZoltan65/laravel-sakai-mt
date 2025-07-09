<?php

namespace App\Helpers;

use App\Models\AppSetting;
use App\Models\CompanySetting;

class Settings
{
    public static function getApp($key, $default = null)
    {
        return AppSetting::where('key', $key)->value('value') ?? $default;
    }
    
    public static function getCompany($key, $default = null)
    {
        return CompanySetting::where('key', $key)->value('value') ?? $default;
    }
    
    public static function setApp($key, $value, $type = 'string')
    {
        return AppSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
    
    public static function setCompany($key, $value, $type = 'string')
    {
        return CompanySetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}