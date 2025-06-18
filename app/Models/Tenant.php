<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;
use Illuminate\Database\Eloquent\Builder;

class Tenant extends BaseTenant
{
    //use UsesTenantConnection;

    protected $connection = 'landlord';

    protected $fillable = [
        'name', 'domain', 'host', 'port', 
        'database', 'username', 'password', 'active'
    ];

    public function getDatabaseName(): string
    {
        return $this->database;
    }
    
    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', 1);
    }
    
    public function scopeToSelect()
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }
}