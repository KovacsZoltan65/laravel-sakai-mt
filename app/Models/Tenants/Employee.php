<?php

namespace App\Models\Tenants;

use App\Models\Company;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory,
        SoftDeletes;

    //protected $connection = 'tenant';
    protected $table = "employees";

    protected $fillable   = ['name', 'position', 'email', 'company_id', 'active'];

    protected static function newFactory(): Factory
    {
        return EmployeeFactory::new();
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function parent(): BelogsToMany
    {
        return $this->belongsToMany(
            self::class,
            'hierarchy',
            'child_id',
            'parent_id'
        );
    }
    
    public function children(): BelogsToMany
    {
        return $this->belongsToMany(
            self::class,
            'hierarchy',
            'parent_id',
            'child_id'
        );
    }
}
