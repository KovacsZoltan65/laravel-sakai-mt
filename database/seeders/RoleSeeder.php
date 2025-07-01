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
        // Szuper admin
        $superadmin = Role::create([
            'name'          => 'superadmin'
        ]);
        $superadmin->givePermissionTo([
            'view user',
            'create user',
            'update user',
            'delete user',

            'view role',
            'create role',
            'update role',
            'delete role',

            'view permission',
            'create permission',
            'update permission',
            'delete permission',

            'view company',
            'create company',
            'update company',
            'delete company',

            'view employee',
            'create employee',
            'update employee',
            'delete employee',

            'view tenant',
            'create tenant',
            'update tenant',
            'delete tenant',
        ]);

        // Admin
        $admin = Role::create([
            'name'          => 'admin'
        ]);
        $admin->givePermissionTo([
            'delete user',
            'update user',
            'view user',
            'create user',
            'view role',
            'view permission',
        ]);

        // Operator
        $operator = Role::create([
            'name'          => 'operator'
        ]);

        $operator->givePermissionTo([
            //'delete user',
            'view user',
            'create user',
            'view role',
            'view permission',
        ]);
    }
}
