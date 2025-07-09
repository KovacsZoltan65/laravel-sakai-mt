<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tenant', 'auth', 'check.tenant.lock'])->group(function() {

    // 🔄 Alapértelmezett redirect a dashboardra
    Route::get('/', fn() => redirect('dashboard'));

    /**
     * ========================================
     * COMPANY SELECTOR – csak auth szükséges!
     * ========================================
     */
    Route::get('/select-company', [App\Http\Controllers\Tenants\CompanyController::class, 'showSelector'])->name('company.selector');
    Route::post(
        '/select-company',
        [
            App\Http\Controllers\Tenants\CompanyController::class,
            'storeSelection'
        ]
    )->name('company.selector.save');

    /**
     * ========================================
     * MINDEN MÁS – cég kiválasztás szükséges!
     * ========================================
     */
    Route::middleware(['ensure.company.selected'])->group(function () {

        /**
         * ==============================================
         * DASHBOARD
         * ==============================================
         */
        Route::get('/dashboard', [App\Http\Controllers\Tenants\TenantController::class, 'dashboard'])->name('dashboard');

        /**
         * ==============================================
         * SETTINGS
         * ==============================================
         */
        Route::middleware(['auth'])->prefix('settings')->as('tenant.')->group(function() {
            // App Settings
            Route::post('/app-settings/fetch', [App\Http\Controllers\Tenants\Settings\AppSettingsController::class, 'fetch'])->name('app_settings.fetch');
            // Company Settings
            Route::post('/company-settings/fetch', [\App\Http\Controllers\Tenants\Settings\CompanySettingsController::class, 'fetch'])->name('comp_settings.fetch');
            // User Settings
            Route::post('/user-settings/fetch', [App\Http\Controllers\Tenants\Settings\UserSettingsController::class, 'fetch'])->name('user_settings.fetch');
        });
        
        /**
         * ==============================================
         * APP SETTINGS
         * ==============================================
         */
        Route::prefix('app_settings')->name('tenant.app_settings.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Tenants\AppSettingController::class, 'index'])->name('index');
            Route::post('/fetch', [App\Http\Controllers\Tenants\AppSettingController::class, 'fetch'])->name('fetch');
        });
        
        /**
         * ==============================================
         * COMPANY SETTINGS
         * ==============================================
         */
        Route::prefix('comp_settings')->name('tenant.comp_settings.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Tenants\CompanySettingController::class, 'index'])->name('index');
            Route::post('/fetch', [\App\Http\Controllers\Tenants\CompanySettingController::class, 'fetch'])->name('fetch');
        });
        
        /**
         * ==============================================
         * USER SETTINGS
         * ==============================================
         */
        Route::prefix('user_settings')->name('tenant.user_settings.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Tenants\UserSettingController::class, 'index'])->name('index');
            Route::post('/fetch', [\App\Http\Controllers\Tenants\UserSettingController::class, 'fetch'])->name('fetch');
        });
        
        /**
         * ==============================================
         * EMPLOYEES
         * ==============================================
         */
        Route::prefix('employees')->name('tenant.employees.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenants\EmployeeController::class, 'index'])->name('index');
            Route::post('/fetch', [App\Http\Controllers\Tenants\EmployeeController::class, 'fetch'])->name('fetch');
            Route::post('/', [App\Http\Controllers\Tenants\EmployeeController::class, 'storeEmployee'])->name('store');
            Route::put('/{id}', [App\Http\Controllers\Tenants\EmployeeController::class, 'updateEmployee'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Tenants\EmployeeController::class, 'deleteEmployee'])->name('delete');
            Route::put('/{id}/restore', [App\Http\Controllers\Tenants\EmployeeController::class, 'restoreEmployee'])->name('restore');
            Route::delete('/{id}/force-delete', [App\Http\Controllers\Tenants\EmployeeController::class, 'realDeleteEmployee'])->name('force-delete');
        });

        /**
         * ==============================================
         * COMPANIES
         * ==============================================
         */
        Route::prefix('companies')->name('tenant.companies.')->group(function () {
            Route::get('/', [App\Http\Controllers\Tenants\CompanyController::class, 'index'])->name('index');
            Route::post('/fetch', [App\Http\Controllers\Tenants\CompanyController::class, 'fetch'])->name('fetch');
            Route::post('/store', [App\Http\Controllers\Tenants\CompanyController::class, 'storeEmployee'])->name('store');
            Route::put('/{id}/update', [App\Http\Controllers\Tenants\CompanyController::class, 'updateEmployee'])->name('update');
            Route::delete('/delete', [App\Http\Controllers\Tenants\CompanyController::class, 'deleteEmployees'])->name('delete.bulk');
            Route::delete('/{id}', [App\Http\Controllers\Tenants\CompanyController::class, 'deleteEmployee'])->name('delete');
            Route::put('/{id}/restore', [App\Http\Controllers\Tenants\CompanyController::class, 'restoreEmployee'])->name('restore');
            Route::delete('/{id}/force-delete', [App\Http\Controllers\Tenants\CompanyController::class, 'realDeleteEmployee'])->name('force-delete');
        });

        /**
         * ==============================================
         * ACS SYSTEMS
         * ==============================================
         */
        Route::get('/acs_systems', [App\Http\Controllers\Tenants\AcsSystemController::class, 'index'])->name('acs_system.index');
        
        /**
         * ==============================================
         * HIERARCHY
         * ==============================================
         */
        Route::prefix('hierarchy')->name('tenant.hierarchy.')->group(function() {
            // Megtekintés
            Route::get('/', [App\Http\Controllers\Tenants\HierarchyController::class, 'index'])->name('index');
            Route::get('/root', [App\Http\Controllers\Tenants\HierarchyController::class, 'root'])->name('root');
            Route::get('/children/{employee}', [App\Http\Controllers\Tenants\HierarchyController::class, 'children'])->name('children');
            Route::get('/search', [\App\Http\Controllers\Tenants\HierarchyController::class, 'search'])->name('search');
            
            // Műveletek
            Route::post('/assign', [\App\Http\Controllers\Tenants\HierarchyController::class, 'assignChild'])->name('assign');
            Route::put('/reassign', [\App\Http\Controllers\Tenants\HierarchyController::class, 'reassignChild'])->name('reassign');
            Route::delete('/remove/{employee}', [\App\Http\Controllers\Tenants\HierarchyController::class, 'removeFromHierarchy'])->name('remove');
        });
    });
});
