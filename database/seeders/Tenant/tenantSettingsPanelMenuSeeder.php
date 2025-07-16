<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tenantSettingsPanelMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = \App\Models\MenuItem::where('label', 'settings')->first();

        if( !$settings ) {
            $this->command->warn("Parent menu 'Settings' not found. Skipping...");
            return;
        }

        // Keressük meg, hogy létezik-e már a 'Settings Panel' menü
        /** @var \App\Models\MenuItem|null $settingsPanel */
        $settingsPanel = $settings->children()
            ->where('label', 'Settings Panel')
            ->first();

        $data = [
            'label' => 'Settings Panel',
            'icon' => 'pi pi-building',
            'can' => 'view setting_panel',
            'url' => null,
            'route_name' => 'tenant.settings_panel.index',
            //'parent_id' => $administration->id,
            'default_weight' => 1,
            'order_index' => 1,
        ];

        if ($settingsPanel) {
            // Csak akkor frissítsünk, ha ténylegesen változott valami
            $dirty = false;
            foreach ($data as $key => $value) {
                if ($settingsPanel->$key !== $value) {
                    $settingsPanel->$key = $value;
                    $dirty = true;
                }
            }

            if( $dirty ) {
                $settingsPanel->save();
                $this->command->info("'Settings Panel' menu updated.");
            } else {
                $this->command->info("'Settings Panel' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $settings->children()->create($data);
            $this->command->info("'Settings Panel' menu created.");
        }
    }
}
