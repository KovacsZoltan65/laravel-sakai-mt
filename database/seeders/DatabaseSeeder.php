<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use App\Models\Tenant;

use Database\Seeders\Hq\hqMenuSeeder;
use Database\Seeders\Hq\hqTenantsMenuSeeder;
use Database\Seeders\Hq\hqEmployeesMenuSeeder;
use Database\Seeders\Hq\hqCompaniesMenuSeeder;
use Database\Seeders\Hq\hqHierarchyMenuSeeder;

use Database\Seeders\Tenant\tenantMenuSeeder;
use Database\Seeders\Tenant\tenantEmployeesMenuSeeder;
use Database\Seeders\Tenant\tenantCompaniesMenuSeeder;
use Database\Seeders\Tenant\tenantTenantsMenuSeeder;
use Database\Seeders\Tenant\tenantHierarchyMenuSeeder;

use Illuminate\Database\Seeder;

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

            // Fontos a sorrend, ne változtasd meg!!!
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // ------------------------------------

            EmailsSeeder::class,

            // A kapcsolat miatt fontos a sorrend
            CompanySeeder::class,
            EmployeeSeeder::class,
            HierarchySeeder::class,

        ]);


        // tenant specifikus seeder
        $tenant = Tenant::current(); // vagy app(Tenant::class)

        if( $tenant->domain === 'hq.mt' ) {
            $this->call([
                //MenuItemSeeder::class,
                // Hq menü
                hqMenuSeeder::class,
                hqTenantsMenuSeeder::class,
                hqEmployeesMenuSeeder::class,
                hqCompaniesMenuSeeder::class,
                hqHierarchyMenuSeeder::class,
                Hq\hqMenuManagerMenuSeeder::class,
                
            ]);
        } else {
            $this->call([
                //EmployeeSeeder::class,
                //MenuItemSeeder::class,

                // Tenant menü
                tenantMenuSeeder::class,
                tenantEmployeesMenuSeeder::class,
                tenantCompaniesMenuSeeder::class,
                tenantHierarchyMenuSeeder::class,

                AcsSystemSeeder::class,

                // Fontos a sorrend, ne változtasd meg!!!

                // ------------------------------------
            ]);
        }
    }
}
