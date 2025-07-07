<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantEmployeesMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();

        if( !$administration ) {
            $this->command->warn("Parent menu 'Administration' not found. Skipping...");
            return;
        }

        // Keressük meg, hogy létezik-e már az 'employees' menü
        /** @var \App\Models\MenuItem|null $companies */
        $employees = $administration->children()
            ->where('label', 'employees')
            ->first();

        $data = [
            'label' => 'employees',
            'url' => null,
            'route_name' => 'tenant.employees.index',
            'icon' => 'pi pi-users',
            'can' => 'view employee',
            'default_weight' => 1,
            'order_index' => 1,
        ];

        if( $employees ) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($employees->$key !== $value) {
                    $employees->$key = $value;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $companies->save();
                $this->command->info("'employees' menu updated.");
            } else {
                $this->command->info("'employees' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create( $data );
            $this->command->info("'employees' menu created.");
        }
    }
}
