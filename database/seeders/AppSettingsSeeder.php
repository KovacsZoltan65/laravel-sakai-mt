<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AppSetting::truncate();
        Schema::enableForeignKeyConstraints();

        $tenant = Tenant::current();
        if( $tenant->name !== 'Hq' ) {

            $settings = [
                ['key' => 'default_locale', 'value' => 'hu', 'type' => 'string'],
                ['key' => 'default_timezone', 'value' => 'Europe/Budapest', 'type' => 'string'],
                ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'bool'],
                ['key' => 'support_email', 'value' => 'support@example.com', 'type' => 'string'],
            ];

            foreach ($settings as $setting) {
                AppSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'type' => $setting['type']
                    ]
                );
            }

        } else {
            $settings = [
                ['key' => 'default_locale', 'value' => 'hu', 'type' => 'string'],
                ['key' => 'default_timezone', 'value' => 'Europe/Budapest', 'type' => 'string'],
                ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'bool'],
                ['key' => 'support_email', 'value' => 'support@example.com', 'type' => 'string'],
            ];

            foreach ($settings as $setting) {
                AppSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'type' => $setting['type']
                    ]
                );
            }
        }
    }
}
