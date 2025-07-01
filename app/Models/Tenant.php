<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;
//use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Tenant extends BaseTenant
{
    //use UsesTenantConnection;

    protected $connection = 'landlord';

    protected $fillable = [
        'name', 'domain', 'host', 'port',
        'database', 'username', 'password', 'active'
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where( 'active', '=', APP_ACTIVE);
    }
    
    public function scopeWithoutHq(Builder $query): Builder
    {
        return $query->where('name', '<>', 'Hq');
    }

    public static function toSelect(): array
    {
        return static::active()
            ->where('name', '<>', 'Hq')
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }
}
