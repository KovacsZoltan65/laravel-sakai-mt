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
        // A tábla csonkítása előtt tiltsa le az idegen kulcsokra vonatkozó korlátozásokat
        Schema::disableForeignKeyConstraints();
        Hierarchy::truncate();
        // Idegenkulcs-korlátozások engedélyezése a tábla csonkítása után
        Schema::enableForeignKeyConstraints();

        // A seedeléshez a jelenlegi tenantet használjuk
        // @var Tenant $tenant
        $tenant = Tenant::current();
        
        if ($tenant->name !== 'Hq') {
            
            // Csak a tenant cégéhez tartozó dolgozók
            $companyId = $employees->first()?->company_id ?? null;
            if (!$companyId) return;

            // Véletlenszer en kiválasztunk egy céget, aminek a dolgozóihoz tartozó hierarchiát
            // szeretnénk létrehozni
            $employees = Employee::where('company_id', $companyId)
                ->inRandomOrder()
                ->get();

            // Ha kevesebb mint 20 dolgozó van, akkor nincs értelme seedelni
            if ($employees->count() < 20) {
                // A seedeléshez legalább 20 dolgozónak kell lennie
                return;
            }

            // A hierarchia csúcsán álló vezérigazgató (CEO)
            $ceo = $employees->shift();

            // A CEO alatt álló felsővezetők
            $topManagers = $employees->splice(0, 5);

            // A felsővezetők alatt álló középszinten dolgozó vezetők
            $middleManagerCount = rand(10, 20);
            $middleManagers = $employees->splice(0, $middleManagerCount);

            // A hierarchia alján dolgozók
            $staff = $employees;

            // A CEO alatt álló felsővezetők
            // A felsővezetők alatt álló dolgozók
            foreach ($topManagers as $manager) {
                // A felsővezetőket a CEO alá rendeljük
                DB::table('hierarchy')->insert([
                    'parent_id' => $ceo->id,
                    'child_id' => $manager->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // A felsővezetők alatt álló középszinten dolgozó vezetőket
            // a felsővezetők alá rendeljük
            foreach ($middleManagers as $index => $manager) {
                // A középszinten dolgozó vezetőknek a felsővezetőket kell
                // a szülőjeként megadni
                $top = $topManagers[$index % $topManagers->count()];
                // A hierarchia beillesztése a DB-be
                DB::table('hierarchy')->insert([
                    'parent_id' => $top->id,
                    'child_id' => $manager->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // A hierarchia alján dolgozók
            // Minden dolgozónak egy vezetőt kell rendelni
            // A vezetőket a felsővezetők alá rendeljük
            foreach ($staff as $employee) {
                // Véletlenszer en választunk ki egy vezetőt
                // a középszinten dolgozó vezetők közül
                $middle = $middleManagers->random();
                // A hierarchia beillesztése a DB-be
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
