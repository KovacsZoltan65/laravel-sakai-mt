<?php

/**
 * Hq applikációs beállítások menüpont
 */

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class hqAppSettingsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $administration = MenuItem::where('label', 'administration')->first();
        
        if( !$administration ) {
            $this->command->warn("Parent menu 'Administration' not found. Skipping...");
            return;
        }
        
        // Keressük meg, hogy létezik-e már a 'companies' menü
        /** @var \App\Models\MenuItem|null $companies */
        $appSettings = $administration->children()
            ->where('label', 'AppSettings')
            ->first();
        
        $data = [
            'label' => 'AppSettings',
            'icon' => 'pi pi-building',
            'can' => 'view hq_app_settings',
            'url' => null,
            'route_name' => 'hq.settings.index',
            'default_weight' => 1,
            'order_index' => 1,
        ];
        
        if( $appSettings ) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($appSettings->$key !== $value) {
                    $appSettings->$key = $value;
                    $dirty = true;
                }
            }
            
            if( $dirty ) {
                $appSettings->save();
                $this->command->info("'AppSettings' menu updated.");
            } else {
                $this->command->info("'AppSettings' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create(array_merge(['label' => 'AppSettings'], $data));
            $this->command->info("'AppSettings' menu created.");
        }
    }
}