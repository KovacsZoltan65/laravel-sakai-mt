<?php

namespace Database\Seeders\Hq;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class hqMenuManagerMenuSeeder extends Seeder
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
        /** @var MenuItem|null $menu_manager */
        $menu_manager = $administration->children()
            ->where('label', 'menu manager')
            ->first();
        
        $data = [
            'label' => 'menu manager',
            'icon' => 'pi pi-bars',
            'can' => 'view menu_manager',
            'url' => null,
            'route_name' => 'hq.menu.manage',
            'default_weight' => 1,
            'order_index' => 5,
        ];
        
        if( $menu_manager ) {
            $dirty = false;
            
            foreach ($data as $key => $value) {
                if ($menu_manager->$key !== $value) {
                    $menu_manager->$key = $value;
                    $dirty = true;
                }
            }
            
            if ($dirty) {
                $menu_manager->save();
                $this->command->info("'menu manager' menu updated.");
            } else {
                $this->command->info("'menu manager' menu already up-to-date.");
            }
        } else {
            // Ha nem létezik, hozzuk létre
            $administration->children()->create($data);
            $this->command->info("'menu manager' menu created.");
        }
    }
}
