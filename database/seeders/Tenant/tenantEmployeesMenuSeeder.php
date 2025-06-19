<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantEmployeesMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();
        
        // Administration - Employees
        $administration->children()->create([
            'label' => 'employees',
            'url' => null,
            'route_name' => 'tenant.employees.index',
            'icon' => 'pi pi-users',
            'can' => 'view employee',
            'default_weight' => 1,
            'order_index' => 1,
        ]);
    }
}