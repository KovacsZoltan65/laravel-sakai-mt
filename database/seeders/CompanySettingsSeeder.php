<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        CompanySetting::truncate();
        Schema::enableForeignKeyConstraints();
        
        $tenant = Tenant::current();
        if( $tenant->name !== 'Hq' ) {
            
            $companies = Company::all();
            foreach( $companies as $company ) {
                $settings = [
                    ['company_id' => $company->id, 'key' => 'workday_length', 'value' => '8', 'type' => 'int'],
                    ['company_id' => $company->id, 'key' => 'vacation_days_per_year', 'value' => '20', 'type' => 'int'],
                    ['company_id' => $company->id, 'key' => 'company_logo_url', 'value' => '', 'type' => 'string'],
                    ['company_id' => $company->id, 'key' => 'enable_overtime', 'value' => '1', 'type' => 'bool'],
                ];

                foreach ($settings as $setting) {
                    CompanySetting::updateOrCreate(
                        [
                            'company_id' => $setting['company_id'],
                            'key' => $setting['key']
                        ],
                        [
                            'company_id' => $setting['company_id'], 
                            'value' => $setting['value'], 
                            'type' => $setting['type']
                        ]
                    );
                }
            }
        }
    }
}
