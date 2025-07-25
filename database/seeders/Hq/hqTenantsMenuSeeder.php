<?php

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class hqTenantsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();
        if( $administration ) {
            // Administration - Tenants
            $administration->children()->create([
                'label' => 'tenants',
                'url' => null,
                'route_name' => 'hq.tenants.index',
                'icon' => 'pi pi-sitemap',
                'can' => 'view tenant',
                'default_weight' => 1,
                'order_index' => 1,
            ]);
        }
    }
}
