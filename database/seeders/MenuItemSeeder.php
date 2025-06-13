<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
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
                //'url' => '/home',
                'route_name' => 'dashboard',
                'default_weight' => 1,
            ]
        );

        $home->children()->create(
            [
                'label' => 'dashboard',
                'url' => '/dashboard',
                'route_name' => '',
                'default_weight' => 1,
            ]
        );

        // Főmenü - Administration
        $administration = MenuItem::create(
            [
            'label' => 'administration',
            'url' => null,
            'route_name' => null,
            'default_weight' => 2,
        ]
        );

        $security = $administration->children()->create(
            [
            'label' => 'security',
            'url' => null,
            'route_name' => null,
            'default_weight' => 1,
        ]
        );

        $security->children()->createMany([
                [
                    'label' => 'users',
                    'url' => '/users',
                    'route_name' => '',
                    'default_weight' => 1,
                ],
                [
                    'label' => 'roles',
                    'url' => '/roles',
                    'route_name' => '',
                    'default_weight' => 2,
                ],
                [
                    'label' => 'permissions',
                    'url' => '/permissions',
                    'route_name' => '',
                    'default_weight' => 3,
                ],
                [
                    'label' => 'activities',
                    'url' => '/activities',
                    'route_name' => '',
                    'default_weight' => 4,
                    ]
            ]);

        // Főmenü - System
        $system = MenuItem::create(
            [
                'label' => 'system',
                'url' => null,
                'route_name' => null,
                'default_weight' => 3,
            ]
        );

        $geo = $system->children()->create(
            [
                'label' => 'geo',
                'url' => null,
                'route_name' => null,
                'default_weight' => 1,
            ]
        );

        $geo->children()->createMany(
            [
                [
                    'label' => 'countries',
                    'url' => '/countries',
                    'route_name' => '',
                    'default_weight' => 1,
                ],
                [
                    'label' => 'regions',
                    'url' => '/regions',
                    'route_name' => '',
                    'default_weight' => 2,
                ],
                [
                    'label' => 'cities',
                    'url' => '/cities',
                    'route_name' => '',
                    'default_weight' => 3,
                ],
            ]
        );

        $system->children()->createMany([
            [
                'label' => 'subdomain_states',
                'url' => '/subdomain_states',
                'route_name' => '',
                'default_weight' => 2,
            ],
            [
                'label' => 'acs_systems',
                'url' => '/acs_systems',
                'route_name' => '',
                'default_weight' => 3,
            ],
            [
                'label' => 'app_settings',
                'url' => '/app_settings',
                'route_name' => '',
                'default_weight' => 4,
            ],
            [
                'label' => 'comp_settings',
                'url' => '/comp_settings',
                'route_name' => '',
                'default_weight' => 5,
            ],
        ]);

        // Főmenü - Specimens
        $specimens = MenuItem::create(
            [
                'label' => 'specimens',
                'url' => null,
                'route_name' => null,
                'default_weight' => 4,
            ]
        );

        $specimens->children()->createMany(
            [
                [
                    'label' => 'companies',
                    'url' => '/companies',
                    'route_name' => '',
                    'default_weight' => 1,
                ],
                [
                    'label' => 'subdomains',
                    'url' => '/subdomains',
                    'route_name' => '',
                    'default_weight' => 2,
                ],
                [
                    'label' => 'entities',
                    'url' => '/entities',
                    'route_name' => '',
                    'default_weight' => 3,
                ],
                [
                    'label' => 'employees',
                    //'url' => '/employees',
                    'route_name' => 'employees.index',
                    'default_weight' => 4,
                ],
            ]
        );
    }
}
