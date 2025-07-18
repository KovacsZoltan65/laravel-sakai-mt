<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;
    
    protected $fillable = ['company_id', 'key', 'value', 'type'];

    protected $casts = [
        'value' => 'string',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
