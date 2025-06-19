<?php

use Illuminate\Support\Facades\Route;

Route::domain('hq.mt')->middleware(['auth'])->group(function() {


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

    Route::get(
        '/dashboard',
        [App\Http\Controllers\Hq\AdminController::class, 'index']
    )->name('dashboard');

    // Tovobbi admin útvonalak...

    /**
     * ========================================
     * TENANT SELECTOR
     * ========================================
     */
    Route::domain('hq.mt')->prefix('/api/hq')
        ->middleware(['auth'])
        ->group(function () {
        Route::get('/tenants', [App\Http\Controllers\Hq\TenantController::class, 'getTenantsToSelect'])->name('tenant.getTenants');
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
    //Route::get('/acs_systems', [App\Http\Controllers\Tenants\AcsSystemController::class, 'index'])->name('hq.acs_system.index');
});
