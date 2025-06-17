<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::create([
            'name'          => 'superadmin'
        ]);
        $superadmin->givePermissionTo([
            'read user',
            'create user',
            'update user',
            'delete user',
            
            'read role',
            'create role',
            'update role',
            'delete role',
            
            'read permission',
            'create permission',
            'update permission',
            'delete permission',
            
            'read employee',
            'create employee',
            'update employee',
            'delete employee',
        ]);
        $admin = Role::create([
            'name'          => 'admin'
        ]);
        $admin->givePermissionTo([
            'delete user',
            'update user',
            'read user',
            'create user',
            'read role',
            'read permission',
        ]);
        
        $operator = Role::create([
            'name'          => 'operator'
        ]);
        
        $operator->givePermissionTo([
            //'delete user',
            'read user',
            'create user',
            'read role',
            'read permission',
        ]);
    }
}
