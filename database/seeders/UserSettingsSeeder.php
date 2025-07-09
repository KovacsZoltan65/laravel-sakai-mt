<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        UserSetting::truncate();
        Schema::enableForeignKeyConstraints();

        $tenant = Tenant::current();

        if( $tenant->name !== 'Hq' ) {
            $users = User::all();

            foreach ($users as $user) {
                $settings = [
                    ['user_id' => $user->id, 'key' => 'locale', 'value' => 'hu', 'type' => 'string'],
                    ['user_id' => $user->id, 'key' => 'theme', 'value' => 'light', 'type' => 'string'],
                ];

                foreach ($settings as $setting) {
                    UserSetting::updateOrCreate(
                        ['user_id' => $setting['user_id'], 'key' => $setting['key']],
                        ['value' => $setting['value'], 'type' => $setting['type']]
                    );
                }
            }
        } else {
            $users = User::all();

            foreach ($users as $user) {
                $settings = [
                    ['user_id' => $user->id, 'key' => 'locale', 'value' => 'hu', 'type' => 'string'],
                    ['user_id' => $user->id, 'key' => 'theme', 'value' => 'light', 'type' => 'string'],
                ];

                foreach ($settings as $setting) {
                    UserSetting::updateOrCreate(
                        ['user_id' => $setting['user_id'], 'key' => $setting['key']],
                        ['value' => $setting['value'], 'type' => $setting['type']]
                    );
                }
            }
        }
    }
}
