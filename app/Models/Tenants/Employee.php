<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\Factory;

class Employee extends Model
{
    use HasFactory,
        SoftDeletes;

    //protected $connection = 'tenant';
    protected $table = "employees";

    protected $fillable   = ['name', 'position', 'email', 'active'];

    protected static function newFactory(): Factory
    {
        return \Database\Factories\EmployeeFactory::new();
    }
}
