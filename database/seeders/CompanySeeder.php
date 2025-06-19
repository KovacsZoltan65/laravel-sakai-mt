<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Külső kulcsok tiltása, tábla ürítése
        Schema::disableForeignKeyConstraints();
        Company::truncate();
        Schema::enableForeignKeyConstraints();
        
        $count = 20;
        
        $this->command->warn("Creating {$count} companies...");
        $this->command->getOutput()->progressStart($count);
        
        for ($i = 0; $i < $count; $i++) {
            $company = Company::factory()->create();

            // Haladás jelzése
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->info("{$count} companies created.");
        
        // Logolás letiltása (ha Spatie Activitylog van használatban)
        //activity()->disableLogging();
        
        // Logolás engedélyezése (ha Spatie Activitylog van használatban)
        //activity()->enableLogging();
    }
}
