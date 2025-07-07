<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantHierarchyMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'Administration')->first();

        if( !$administration ) {
            $this->command->warn("Parent menu 'Administration' not found. Skipping...");
            return;
        }

        // Keressük meg, hogy létezik-e már a 'hierarchy' menü
        /** @var \App\Models\MenuItem|null $hierarchy */
        $hierarchy = $administration->children()
            ->where('label', 'hierarchy')
            ->first();

        $data = [
            'label' => 'hierarchy',
            'icon' => 'pi pi-share-alt',
            'can' => 'view hierarchy',
            'url' => null,
            'route_name' => 'tenant.hierarchy.index',
            'default_weight' => 1,
            'order_index' => 3,
        ];

        if( $hierarchy ) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($hierarchy->$key !== $value) {
                    $hierarchy->$key = $value;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $hierarchy->save();
                $this->command->info("'hierarchy' menu updated.");
            } else {
                $this->command->info("'hierarchy' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create( $data );
            $this->command->info("'employees' menu created.");
        }
    }
}
