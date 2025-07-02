<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hierarchy extends Model
{
    use HasFactory,
        SoftDeletes;
    
    protected $table = "hierarchy";
    protected $fillable = ['parent_id', 'child_id'];
    
    
}
