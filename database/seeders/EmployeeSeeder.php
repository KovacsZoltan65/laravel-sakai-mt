<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenants\Employee;
//use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Employee::truncate();
        Schema::enableForeignKeyConstraints();

        $tenant = Tenant::current();
        if( $tenant->name !== 'Hq' ) {
            $count = 1500;
            $this->command->warn("Creating {$count} employees...");
            $this->command->getOutput()->progressStart($count);
            
            Employee::factory()
                ->count(1500)->create()
                ->each(
                    fn () => $this->command->getOutput()->progressAdvance()
                );
            
            $this->command->getOutput()->progressFinish();
            $this->command->info("{$count} employees created.");
        }
    }
}
