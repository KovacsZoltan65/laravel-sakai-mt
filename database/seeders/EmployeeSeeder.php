<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenants\Employee;
use Carbon\Carbon;
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

        $now = Carbon::now();

        $tenant = Tenant::current();

        if( $tenant->name === 'Company 01' ) {
            Employee::insert([
                ['name' => 'John Doe','position' => 'CEO','email' => '2F4R2@example.com','created_at' => $now,    'updated_at' => $now,],
                ['name' => 'Jane Doe','position' => 'CTO','email' => '2F4R3@example.com','created_at' => $now,    'updated_at' => $now,],
                ['name' => 'Mark Doe','position' => 'CTO','email' => '2F4R4@example.com','created_at' => $now,    'updated_at' => $now,],
            ]);
        } elseif( $tenant->name === 'Company 02' ) {
            Employee::insert([
                ['name' => 'Bill Doe','position' => 'CTO','email' => '2F4R5@example.com','created_at' => $now,    'updated_at' => $now,],
                ['name' => 'Bob Doe','position' => 'CTO','email' => '2F4R6@example.com','created_at' => $now,    'updated_at' => $now,],
                ['name' => 'Sue Doe','position' => 'CTO','email' => '2F4R7@example.com','created_at' => $now,    'updated_at' => $now,],
            ]);
        }
    }
}