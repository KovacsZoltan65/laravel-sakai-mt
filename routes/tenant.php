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
            Route::get('/', [App\Http\Controllers\Tenants\HierarchyController::class, 'index'])->name('index');
            Route::get(
                '/children/{employee}', 
                [App\Http\Controllers\Tenants\HierarchyController::class, 'children'
            ])->name('children');
        });
    });
});
