<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class tenantMenuSeeder extends Seeder
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
                'order_index' => 0,
            ]
        );

        // Főmenü - Administration
        $administration = MenuItem::create(
            [
                'label' => 'administration',
                'url' => null,
                'route_name' => null,
                'icon' => 'pi pi-cog',
                'default_weight' => 1,
                'order_index' => 2,
            ]
        );

        // Administration - Employees
//        $administration->children()->create([
//            'label' => 'employees',
//            'url' => null,
//            'route_name' => 'tenant.employees.index',
//            'icon' => 'pi pi-users',
//            'can' => 'view employee',
//            'default_weight' => 1,
//            'order_index' => 1,
//        ]);

        /*
        $administration->children()->createMany(
            [
                [ 'label' => 'users', 'url' => '/users', 'default_weight' => 1, ],
                [ 'label' => 'roles', 'url' => '/roles', 'default_weight' => 2, ],
                [ 'label' => 'permissions', 'url' => '/permissions', 'default_weight' => 3, ],
                [ 'label' => 'activities', 'url' => '/activities', 'default_weight' => 4, ]
            ]
        );

        // Főmenü - System
        $system = MenuItem::create(
            [ 'label' => 'system', 'url' => null, 'default_weight' => 3, ]
        );

        $geo = $system->children()->create(
            [ 'label' => 'geo', 'url' => null, 'default_weight' => 1, ]
        );

        $geo->children()->createMany(
            [
                [ 'label' => 'countries', 'url' => '/countries', 'default_weight' => 1, ],
                [ 'label' => 'regions', 'url' => '/regions', 'default_weight' => 2, ],
                [ 'label' => 'cities', 'url' => '/cities', 'default_weight' => 3, ],
            ]
        );

        $system->children()->createMany([
            [ 'label' => 'subdomain_states', 'url' => '/subdomain_states', 'default_weight' => 2, ],
            [ 'label' => 'acs_systems', 'url' => '/acs_systems', 'default_weight' => 3, ],
            [ 'label' => 'app_settings', 'url' => '/app_settings', 'default_weight' => 4, ],
            [ 'label' => 'comp_settings', 'url' => '/comp_settings', 'default_weight' => 5, ],
        ]);

        // Főmenü - Specimens
        $specimens = MenuItem::create(
            [ 'label' => 'specimens', 'url' => null, 'default_weight' => 4, ]
        );

        $specimens->children()->createMany(
            [
                [ 'label' => 'companies', 'url' => '/companies', 'default_weight' => 1, ],
                [ 'label' => 'subdomains', 'url' => '/subdomains', 'default_weight' => 2, ],
                [ 'label' => 'entities', 'url' => '/entities', 'default_weight' => 3, ],
            ]
        );


        // Security
        $security =
        */

    }
}
