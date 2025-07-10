<?php

namespace App\Helpers;

use DateTimeZone;

class TimezoneHelper
{
    /**
     * Csak európai időzónák visszaadása.
     */
    public static function european(): array
    {
        return array_filter(DateTimeZone::listIdentifiers(), function ($tz) {
            return str_starts_with($tz, 'Europe/');
        });
    }
    
    /**
     * Teljes lista visszaadása (ha mégis kellene)
     */
    public static function all(): array
    {
        return DateTimeZone::listIdentifiers();
    }
}
