<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenants\Employee;
use App\Models\Tenants\Hierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    Schema::disableForeignKeyConstraints();
    Hierarchy::truncate();
    Schema::enableForeignKeyConstraints();

    $tenant = Tenant::current();

    if ($tenant->name !== 'Hq') {
        // Minden distinct cégre
        $companyIds = Employee::select('company_id')->distinct()->pluck('company_id');

        foreach ($companyIds as $companyId) {
            $employees = Employee::where('company_id', $companyId)
                ->inRandomOrder()
                ->get();

            if ($employees->count() < 20) {
                // Túl kevés dolgozó → skip
                continue;
            }

            // CEO
            $ceo = $employees->shift();

            DB::table('hierarchy')->insert([
                'parent_id' => null,
                'child_id' => $ceo->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Top menedzserek
            $topManagers = $employees->splice(0, 5);

            // Középvezetők
            $middleManagerCount = rand(10, 20);
            $middleManagers = $employees->splice(0, $middleManagerCount);

            // Maradék dolgozók
            $staff = $employees;

            // CEO → Top
            foreach ($topManagers as $manager) {
                DB::table('hierarchy')->insert([
                    'parent_id' => $ceo->id,
                    'child_id' => $manager->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Top → Middle
            foreach ($middleManagers as $index => $manager) {
                $top = $topManagers[$index % $topManagers->count()];
                DB::table('hierarchy')->insert([
                    'parent_id' => $top->id,
                    'child_id' => $manager->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Middle → Staff
            foreach ($staff as $employee) {
                $middle = $middleManagers->random();
                DB::table('hierarchy')->insert([
                    'parent_id' => $middle->id,
                    'child_id' => $employee->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

}
