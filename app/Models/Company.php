<?php

namespace App\Models;

use App\Models\Tenants\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Company extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [ 'name', 'email', 'address', 'phone', 'active', ];
    
    protected $casts = [
        'active' => 'integer',
        '' => '',
        '' => '',
    ];
    
    public function scopeActive(Builder $builder): Builder
    {
        return $query->where('active', '=', APP_ACTIVE);
    }
    
    public function toSelect()
    {
        return static::active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }
    
    public function entities()
    {
        return $this->hasMany(Employee::class);
    }
    
    public function getCreatedAtAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->attributes['created_at']));
    }
    
    public function getUpdatedAtAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->attributes['updated_at']));
    }
}
