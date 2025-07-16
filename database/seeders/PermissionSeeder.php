<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view company']);
        Permission::create(['name' => 'create company']);
        Permission::create(['name' => 'update company']);
        Permission::create(['name' => 'delete company']);

        Permission::create(['name' => 'view employee']);
        Permission::create(['name' => 'create employee']);
        Permission::create(['name' => 'update employee']);
        Permission::create(['name' => 'delete employee']);

        Permission::create(['name' => 'view tenant']);
        Permission::create(['name' => 'create tenant']);
        Permission::create(['name' => 'update tenant']);
        Permission::create(['name' => 'delete tenant']);

        Permission::create(['name' => 'view hierarchy']);
        Permission::create(['name' => 'create hierarchy']);
        Permission::create(['name' => 'update hierarchy']);
        Permission::create(['name' => 'delete hierarchy']);

        Permission::create(['name' => 'view setting_panel']);
        //Permission::create(['name' => 'create setting_panel']);
        //Permission::create(['name' => 'update setting_panel']);
        //Permission::create(['name' => 'delete setting_panel']);
        
        Permission::create(['name' => 'view app_setting']);
        Permission::create(['name' => 'create app_setting']);
        Permission::create(['name' => 'update app_setting']);
        Permission::create(['name' => 'delete app_setting']);

        Permission::create(['name' => 'view company_setting']);
        Permission::create(['name' => 'create company_setting']);
        Permission::create(['name' => 'update company_setting']);
        Permission::create(['name' => 'delete company_setting']);
        
        Permission::create(['name' => 'view user_setting']);
        Permission::create(['name' => 'create user_setting']);
        Permission::create(['name' => 'update user_setting']);
        Permission::create(['name' => 'delete user_setting']);
    }
}
