<?php

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class hqMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        MenuItem::truncate();
        Schema::enableForeignKeyConstraints();

        // Főmenü - Home
        $home = MenuItem::create(
            [
                'label' => 'home',
                'url' => null,
                'route_name' => null,
                'icon' => 'pi pi-home',
                'default_weight' => 1,
                'order_index' => 1,
            ]
        );

        // Főmenü - dashboard
        $home->children()->create(
            [
                'label' => 'dashboard',
                'url' => null,
                'route_name' => 'dashboard',
                'icon' => 'pi pi-th-large',
                'default_weight' => 1,
                'order_index' => 2,
            ]
        );

        // Főmenü - Administration
        $administration = MenuItem::create([
            'label' => 'administration',
            'url' => null,
            'route_name' => null,
            'icon' => 'pi pi-cog',
            'default_weight' => 1,
            'order_index' => 1,
        ]);
    }
}
