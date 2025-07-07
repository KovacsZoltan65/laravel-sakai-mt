<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantCompanySettingsMenuSeeder extends Seeder
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
        $compSettings = $administration->children()
            ->where('label', 'CompanySettings')
            ->first();
        
        $data = [
            'label' => 'CompanySettings',
            'icon' => 'pi pi-building',
            'can' => 'view comp_settings',
            'url' => null,
            'route_name' => 'tenant.comp_settings.index',
            'default_weight' => 1,
            'order_index' => 2,
        ];
        
        if( $compSettings ) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($compSettings->$key !== $value) {
                    $compSettings->$key = $value;
                    $dirty = true;
                }
            }
            
            if( $dirty ) {
                $compSettings->save();
                $this->command->info("'CompanySettings' menu updated.");
            } else {
                $this->command->info("'CompanySettings' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create(array_merge(['label' => 'CompanySettings'], $data));
            $this->command->info("'CompanySettings' menu created.");
        }
    }
}