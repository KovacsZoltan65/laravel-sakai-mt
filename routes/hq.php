<?php

use Illuminate\Support\Facades\Route;

Route::domain('hq.mt')->middleware(['auth', 'check.tenant.lock'])->group(function() {


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::resource('/user', App\Http\Controllers\UserController::class)->except('create', 'show', 'edit');
        Route::post('/user/destroy-bulk', [App\Http\Controllers\UserController::class, 'destroyBulk'])->name('user.destroy-bulk');

        Route::resource('/role', App\Http\Controllers\RoleController::class)->except('create', 'show', 'edit');

        Route::resource('/permission', App\Http\Controllers\PermissionController::class)->except('create', 'show', 'edit');

    });


    Route::get('/', fn() => redirect('dashboard') );

    Route::get('/dashboard',[App\Http\Controllers\Hq\AdminController::class, 'index'])->name('dashboard');

    // Tovobbi admin útvonalak...

    /**
     * ========================================
     * SELECTOROK
     * ========================================
     */
    Route::domain('hq.mt')->prefix('/api/hq')
        ->middleware(['auth'])
        ->group(function () {

        // Példány választó
        Route::get('/tenants', [App\Http\Controllers\Hq\TenantController::class, 'getTenantsToSelect'])->name('tenant.getTenants');

        // Cég választó
        Route::get('/companies', [\App\Http\Controllers\Hq\CompanyController::class, 'getCompaniesToSelect'])->name('companies.getCompanies');
    });

    /**
     * ==============================================
     * TENANTS
     * ==============================================
     */
     Route::prefix('tenants')->name('hq.tenants.')->group(function() {
        Route::get('/', [App\Http\Controllers\Hq\TenantController::class, 'index'])->name('index');
        Route::post('/fetch', [App\Http\Controllers\Hq\TenantController::class, 'fetch'])->name('fetch');
        Route::post('/', [App\Http\Controllers\Hq\TenantController::class, 'storeTenant'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Hq\TenantController::class, 'updateTenant'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Hq\TenantController::class, 'deleteTenant'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Hq\TenantController::class, 'restoreTenant'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Hq\TenantController::class, 'realDeleteTenant'])->name('force-delete');
     });

    /**
     * ========================================
     * EMPLOYEES
     * ========================================
     */
    Route::prefix('employees')->name('hq.employees.')->group(function () {
        Route::get('/', [App\Http\Controllers\Hq\EmployeeController::class, 'index'])->name('index');
        Route::post('/fetch', [App\Http\Controllers\Hq\EmployeeController::class, 'fetch'])->name('fetch');
        Route::post('/store', [App\Http\Controllers\Hq\EmployeeController::class, 'storeEmployee'])->name('store');
        Route::put('/{id}/update', [App\Http\Controllers\Hq\EmployeeController::class, 'updateEmployee'])->name('update');
        Route::delete('/delete', [App\Http\Controllers\Hq\EmployeeController::class, 'deleteEmployees'])->name('delete.bulk');
        Route::delete('/{id}', [App\Http\Controllers\Hq\EmployeeController::class, 'deleteEmployee'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Hq\EmployeeController::class, 'restoreEmployee'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Hq\EmployeeController::class, 'realDeleteEmployee'])->name('force-delete');
    });

    /**
     * ========================================
     * COMPANIES
     * ========================================
     */
    Route::prefix('companies')->name('hq.companies.')->group(function() {
        Route::get('/', [App\Http\Controllers\Hq\CompanyController::class, 'index'])->name('index');
        Route::post('/fetch', [App\Http\Controllers\Hq\CompanyController::class, 'fetch'])->name('fetch');
        Route::post('/store', [App\Http\Controllers\Hq\CompanyController::class, 'storeEmployee'])->name('store');
        Route::put('/{id}/update', [App\Http\Controllers\Hq\CompanyController::class, 'updateEmployee'])->name('update');
        Route::delete('/delete', [App\Http\Controllers\Hq\CompanyController::class, 'deleteEmployees'])->name('delete.bulk');
        Route::delete('/{id}', [App\Http\Controllers\Hq\CompanyController::class, 'deleteEmployee'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Hq\CompanyController::class, 'restoreEmployee'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Hq\CompanyController::class, 'realDeleteEmployee'])->name('force-delete');
    });

    /**
     * ========================================
     * ACS SYSTEMS
     * ========================================
     */
    Route::prefix('acs_systems')->name('hq.acs_systems.')->group(function() {
        Route::get('/', [App\Http\Controllers\Hq\AcsSystemController::class, 'index'])->name('index');
        Route::post('/fetch', [App\Http\Controllers\Hq\AcsSystemController::class, 'fetch'])->name('fetch');
        Route::post('/store', [App\Http\Controllers\Hq\AcsSystemController::class, 'storeAcsSystem'])->name('store');
        Route::put('/{id}/update', [App\Http\Controllers\Hq\AcsSystemController::class, 'updateAcsSystem'])->name('update');
        Route::delete('/delete', [App\Http\Controllers\Hq\AcsSystemController::class, 'deleteAcsSystems'])->name('delete.bulk');
        Route::delete('/{id}', [App\Http\Controllers\Hq\AcsSystemController::class, 'deleteAcsSystem'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Hq\AcsSystemController::class, 'restoreAcsSystem'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Hq\AcsSystemController::class, 'realDeleteAcsSystem'])->name('force-delete');
    });
    
    /**
     * ==============================================
     * HIERARCHY
     * ==============================================
     */
    Route::prefix('hierarchy')->name('hq.hierarchy')->group(function() {
        Route::get('/', [\App\Http\Controllers\Hq\HierarchyController::class, 'index'])->name('index');
    });
    Route::get('/hierarchy/{employee}', [\App\Http\Controllers\Hq\HierarchyController::class, 'children'])->name('children');
});
