<?php

namespace Database\Seeders\Tenant;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class tenantSettingMenuSeeder extends Seeder
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
        /** @var MenuItem|null $settings */
        $settings = $administration->children()
            ->where('label', 'Settings')->first();
        
        $data = [
            'label' => 'Settings',
            'icon' => 'pi pi-building',
            'can' => 'view settings',
            'url' => null,
            'route_name' => null,
            'default_weight' => 1,
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
            $administration->children()->create($data);
            $this->command->info("'settings' menu created.");
        }
    }
}
