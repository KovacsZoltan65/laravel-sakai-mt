<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantHierarchyMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();
        
        // Administration - Hierarchy
        $administration->children()->create([
            'label' => 'hierarchy',
            'url' => null,
            'route_name' => 'tenant.hierarchy.index',
            'icon' => 'pi pi-share-alt',
            'can' => 'view hierarchy',
            'default_weight' => 1,
            'order_index' => 3,
        ]);
    }
}