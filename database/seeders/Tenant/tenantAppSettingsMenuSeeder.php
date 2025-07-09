<?php

/**
 * Céges applikációs beállítások menüpont
 */

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantAppSettingsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $settings = MenuItem::where('label', 'settings')->first();
        
        if( !$settings ) {
            $this->command->warn("Parent menu 'settings' not found. Skipping...");
            return;
        }
        
        // Keressük meg, hogy létezik-e már a 'companies' menü
        /** @var \App\Models\MenuItem|null $companies */
        $appSettings = $settings->children()
            ->where('label', 'AppSettings')
            ->first();
        
        $data = [
            'label' => 'AppSettings',
            'icon' => 'pi pi-building',
            'can' => 'view app_setting',
            'url' => null,
            'route_name' => 'tenant.app_settings.index',
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
            $settings->children()->create(array_merge(['label' => 'AppSettings'], $data));
            $this->command->info("'AppSettings' menu created.");
        }
    }
}