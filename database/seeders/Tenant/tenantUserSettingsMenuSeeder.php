<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantUserSettingsMenuSeeder extends Seeder
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
        $userSettings = $settings->children()
            ->where('label', 'UserSettings')
            ->first();
        
        $data = [
            'label' => 'UserSettings',
            'icon' => 'pi pi-building',
            'can' => 'view user_setting',
            'url' => null,
            'route_name' => 'tenant.user_settings.index',
            //'parent_id' => $administration->id,
            'default_weight' => 1,
            'order_index' => 2,
        ];
        
        if( $userSettings ) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($userSettings->$key !== $value) {
                    $userSettings->$key = $value;
                    $dirty = true;
                }
            }
            
            if( $dirty ) {
                $userSettings->save();
                $this->command->info("'UserSettings' menu updated.");
            } else {
                $this->command->info("'UserSettings' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $settings->children()->create($data);
            $this->command->info("'UserSettings' menu created.");
        }
    }
}