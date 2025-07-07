<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';

    protected $fillable = [
        'label', 'icon', 'can', 'url', 'route_name',
        'default_weight', 'parent_id', 'order_index'
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', 1);
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order_index');
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MenuItemUsage::class);
    }
}
