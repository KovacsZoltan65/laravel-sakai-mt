<?php

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class hqCompaniesMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();

        if( !$administration ) {
            $this->command->warn("Parent menu 'Administration' not found. Skipping...");
            return;
        }

        // Keressük meg, hogy létezik-e már a 'companies' menü
        /** @var \App\Models\MenuItem|null $companies */
        $companies = $administration->children()
            ->where('label', 'companies')
            ->first();

        $data = [
            'label' => 'companies',
            'icon' => 'pi pi-building',
            'can' => 'view company',
            'url' => null,
            'route_name' => 'hq.companies.index',
            'default_weight' => 1,
            'order_index' => 2,
        ];

        if ($companies) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($companies->$key !== $value) {
                    $companies->$key = $value;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $companies->save();
                $this->command->info("'companies' menu updated.");
            } else {
                $this->command->info("'companies' menu already up-to-date.");
            }

        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create(array_merge(['label' => 'companies'], $data));
            $this->command->info("'companies' menu created.");
        }
    }
}
