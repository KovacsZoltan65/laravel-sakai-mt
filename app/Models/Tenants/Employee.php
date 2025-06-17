<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,
        SoftDeletes;

    //protected $connection = 'tenant';
    protected $fillable   = ['name', 'position', 'email', 'active'];
}