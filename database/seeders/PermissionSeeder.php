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
        
        Permission::create(['name' => 'view employee']);
        Permission::create(['name' => 'create employee']);
        Permission::create(['name' => 'update employee']);
        Permission::create(['name' => 'delete employee']);
        
        Permission::create(['name' => 'view acs_system']);
        Permission::create(['name' => 'create acs_system']);
        Permission::create(['name' => 'update acs_system']);
        Permission::create(['name' => 'delete acs_system']);
    }
}
