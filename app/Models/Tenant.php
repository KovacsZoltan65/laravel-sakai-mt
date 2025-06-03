<?php

namespace App\Models;

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

    public function getDatabaseName(): string
    {
        return $this->database;
    }
}