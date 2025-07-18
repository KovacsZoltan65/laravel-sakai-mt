<?php

namespace Database\Seeders;

use App\Models\SettingsMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SettingsMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        SettingsMeta::truncate();
        Schema::enableForeignKeyConstraints();
        
        DB::table('settings_meta')->insert([
            [
                'key' => 'locale',
                'type' => 'locale',
                'scope' => 'user',
                'default_value' => 'hu',
                'description' => 'Felhasználó által használt nyelv',
            ],
            [
                'key' => 'timezone',
                'type' => 'timezone',
                'scope' => 'company',
                'default_value' => 'Europe/Budapest',
                'description' => 'Cég időzónája',
            ],
            [
                'key' => 'theme',
                'type' => 'theme',
                'scope' => 'user',
                'default_value' => 'system',
                'description' => 'Alkalmazás témája (világos/sötét)',
            ],
            [
                'key' => 'work_start_hour',
                'type' => 'int',
                'scope' => 'company',
                'default_value' => '8',
                'description' => 'Munkakezdés órában',
            ],
            [
                'key' => 'use_custom_logo',
                'type' => 'bool',
                'scope' => 'app',
                'default_value' => 'false',
                'description' => 'Egyedi logó használata az egész rendszerben',
            ],
        ]);
    }
}
