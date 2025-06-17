<?php

namespace Database\Seeders;

use App\Models\AcsSystem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AcsSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AcsSystem::truncate();
        Schema::enableForeignKeyConstraints();
        
        //activity()->disableLogging();
        
        $arr_acs = [
            ['id' => 1, 'name' => 'N',              'active' => 1],
            ['id' => 2, 'name' => 'WinAccess',      'active' => 1],
            ['id' => 3, 'name' => 'Enviromux',      'active' => 1],
            ['id' => 4, 'name' => 'Siport',         'active' => 1],
            ['id' => 5, 'name' => 'WinAccess (WC)', 'active' => 1],
            ['id' => 6, 'name' => 'GenerallyACS',   'active' => 1],
        ];
        
        $count = count($arr_acs);
        $this->command->warn(PHP_EOL . __('migration_creating_acs_systems') );
        
        $this->command->getOutput()->progressStart($count);
        foreach($arr_acs as $acs)
        {
            AcsSystem::factory()->create($acs);
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->info(PHP_EOL . __('migration_created_acs_systems') );
        
        //activity()->enableLogging();
    }
}
