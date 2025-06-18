<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
//use Spatie\Activitylog\Traits\LogsActivity;
//use Spatie\Activitylog\LogOptions;
use Override;

class AcsSystem extends Model
{
    use HasFactory, 
        SoftDeletes; 
    //use LogsActivity;
    protected $table = 'acs_systems';
    protected $fillable = ['name', 'active'];
    protected $casts = [
        'active' => 'integer',
    ];

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */
/*
    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'acs_systems';
    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];
*/
    /*
     * ==============================================================
     */

    //public static function getTag(): string
    //{
    //    return self::$logName;
    //}

//    public function scopeSearch(Builder $query, Request $request): Builder
//    {
//        return $query->when($request->search, function ($query) use ($request) {
//            $query->where(function ($query) use ($request) {
//                $query->where('name', 'like', "%{$request->search}%");
//            });
//        });
//    }
//
//    public function scopeActive(Builder $query): Builder
//    {
//        return $query->where('active', '=', 1);
//    }

    //#[Override]
    //public function getActivitylogOptions(): LogOptions {
    //    return LogOptions::defaults()
    //        ->logFillable()
    //        ->logAll();
    //}
}
