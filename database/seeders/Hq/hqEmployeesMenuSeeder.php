<?php

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class hqEmployeesMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();

        // Administration - Employees
        $administration->children()->create([
            'label' => 'employees',
            'url' => null,
            'route_name' => 'hq.employees.index',
            'icon' => 'pi pi-users',
            'can' => 'view employee',
            'default_weight' => 1,
            'order_index' => 2,
        ]);

    }
}
