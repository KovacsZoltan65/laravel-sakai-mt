<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsMeta extends Model
{
    use HasFactory;
    
    protected $table = "settings_meta";
    
    protected $fillable = [
        'key', 'type', 'scope', 'description',
        'default_value', 'options', 'dependencies'
    ];

    protected $casts = [
        'options' => 'array',
        'dependencies' => 'array',
    ];
}
