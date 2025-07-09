<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSetting extends Model
{
    use HasFactory,
        SoftDeletes;
    
    protected $table = 'user_settings';
    protected $fillable = ['user_id', 'key', 'value', 'type', 'active'];
    protected $casts = [
        'active' => 'integer',
    ];
    
    public function getCreatedAtAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute()
    {
        return date('Y-m-d H:i', strtotime($this->attributes['updated_at']));
    }
}
