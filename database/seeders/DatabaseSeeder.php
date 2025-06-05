<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tenants\Employee;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Tenant::checkCurrent()
            ? $this->runTenantSpecificSeeders()
            : $this->runLandlordSpecificSeeders();

        // \App\Models\User::factory(10)->create();
        /*
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
        */
    }

    public function runLandlordSpecificSeeders()
    {
        $this->call([
            TenantSeeder::class,
        ]);
    }

    public function runTenantSpecificSeeders()
    {
        // run tenant specific seeders

        $this->call([
            EmployeeSeeder::class,

            // Fontos a sorrend, ne változtasd meg!!!
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // ------------------------------------

            EmailsSeeder::class,

            MenuItemSeeder::class,
        ]);


        // tenant specifikus seeder
        $tenant = Tenant::current(); // vagy app(Tenant::class)
/*
        if (!$tenant) {
            $this->command->error('No tenant context available.');
            return;
        }

        match ($tenant->domain) {
            'company_01.localhost' => $this->call(Company01Seeder::class),
            'company_02.localhost' => $this->call(Company02Seeder::class),
            default => $this->call(DefaultTenantSeeder::class),
        };

        vagy:

        if ($tenant->domain === 'company_01.localhost') {
            $this->call(Company01Seeder::class);
        } elseif ($tenant->domain === 'company_02.localhost') {
            $this->call(Company02Seeder::class);
        } else {
            $this->call(DefaultTenantSeeder::class);
        }
*/
        if( $tenant->domain === 'hq.localhost' ) {
            $this->call([
                //
            ]);
        } else {
            $this->call([
                EmployeeSeeder::class,

                // Fontos a sorrend, ne változtasd meg!!!

                // ------------------------------------
            ]);
        }
/*
        vagy:

        match ($tenant->id) {
            'tenant_abc' => $this->call(TenantAbcSeeder::class),
            default => $this->call(DefaultTenantSeeder::class),
        };

        */

    }
}
