<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class tenantSettingsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'administration')->first();
        
        if( !$administration ) {
            $this->command->warn("Parent menu 'Administration' not found. Skipping...");
            return;
        }
        
        // Keressük meg, hogy létezik-e már a 'companies' menü
        /** @var \App\Models\MenuItem|null $settings */
        $settings = $administration->children()
            ->where('label', 'settings')
            ->first();
        
        $data = [
            'label' => 'settings',
            'icon' => 'pi pi-building',
            'can' => null,
            'url' => null,
            'route_name' => null,
            //'parent_id' => $administration->id,
            'default_weight' => 0,
            'order_index' => 4,
        ];
        
        if ($settings) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($settings->$key !== $value) {
                    $settings->$key = $value;
                    $dirty = true;
                }
            }
            
            if ($dirty) {
                $settings->save();
                $this->command->info("'settings' menu updated.");
            } else {
                $this->command->info("'settings' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create( $data );
            $this->command->info("'settings' menu created.");
        }
    }
}