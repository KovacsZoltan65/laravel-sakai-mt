<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        /*
        Tenant::insertOrIgnore([
            'name'       => 'Hq', 'domain'     => 'hq.tenant', 'database' => 'hq',
            'username'   => 'hq', 'password'   => 'Pa$$w0rd',  'active'   => 1,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        Tenant::insertOrIgnore([
            'name'       => 'Company 01', 'domain'     => 'company-01.tenant', 'database' => 'company_01',
            'username'   => 'company_01', 'password'   => 'Pa$$w0rd',          'active'   => 1,
            'created_at' => $now,         'updated_at' => $now,
        ]);
        Tenant::insertOrIgnore([
            'name'       => 'Company 02', 'domain'     => 'company-02.tenant', 'database' => 'company_02',
            'username'   => 'company_02', 'password'   => 'Pa$$w0rd',          'active'   => 1,
            'created_at' => $now,         'updated_at' => $now,
        ]);
        */

        //
        Tenant::insertOrIgnore([
            'name'       => 'Hq',    'domain'   => 'hq.mt',    'database' => 'hq_mt',
            'username'   => 'hq_mt', 'password' => 'Pa$$w0rd', 'active'   => 1,
            'created_at' => $now, 'updated_at'  => $now,
        ]);
        Tenant::insertOrIgnore([
            'name'       => 'Company 01',    'domain'      => 'company-01.mt', 'database' => 'company_mt_01',
            'username'   => 'company_mt_01', 'password'    => 'Pa$$w0rd',      'active'   => 1,
            'created_at' => $now,            'updated_at'  => $now,
        ]);
        Tenant::insertOrIgnore([
            'name'       => 'Company 02',    'domain'      => 'company-02.mt', 'database' => 'company_mt_02',
            'username'   => 'company_mt_02', 'password'    => 'Pa$$w0rd',      'active'   => 1,
            'created_at' => $now,            'updated_at'  => $now,
        ]);
        //

        /*
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (1, 'Hq', 'hq.tenant', 'localhost', 3306, 'hq', 'hq', 'Pa$$w0rd', 1, '2025-05-28 07:53:53', '2025-05-28 07:53:56');
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (2, 'Company 01', 'company-01.tenant', 'localhost', 3306, 'company_01', 'company_01', 'Pa$$w0rd', 1, '2025-05-28 07:54:40', '2025-05-28 07:54:43');
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (3, 'Company 02', 'company-02.tenant', 'localhost', 3306, 'company_02', 'company_02', 'Pa$$w0rd', 1, '2025-05-28 07:55:23', '2025-05-28 07:55:26');
        */
        /*
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (1, 'Hq', 'hq.mt', 'localhost', 3306, 'hq', 'hq', 'Pa$$w0rd', 1, '2025-05-28 07:53:53', '2025-05-28 07:53:56');
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (2, 'Company 01', 'company-01.mt', 'localhost', 3306, 'company_01', 'company_01', 'Pa$$w0rd', 1, '2025-05-28 07:54:40', '2025-05-28 07:54:43');
        INSERT INTO landlord.tenants(id, name, domain, host, port, `database`, username, password, active, created_at, updated_at) VALUES
        (3, 'Company 02', 'company-02.mt', 'localhost', 3306, 'company_02', 'company_02', 'Pa$$w0rd', 1, '2025-05-28 07:55:23', '2025-05-28 07:55:26');
        */
    }
}
