<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tenant', 'auth'])->group(function() {

    Route::get('/', fn() => redirect('dashboard') );

    Route::get( '/dashboard', [\App\Http\Controllers\TenantController::class, 'index'] )->name('dashboard');

    /**
     * ========================================
     * EMPLOYEES
     * ========================================
     */
    Route::prefix('employees')->name('tenant.employees.')->group(function () {
        Route::get('/', [App\Http\Controllers\Tenants\EmployeeController::class, 'index'])->name('index');

        Route::post('/fetch', [App\Http\Controllers\Tenants\EmployeeController::class, 'fetch'])->name('fetch');
        Route::post('/', [App\Http\Controllers\Tenants\EmployeeController::class, 'storeEmployee'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Tenants\EmployeeController::class, 'updateEmployee'])->name('update');
        Route::delete('/{id}',[App\Http\Controllers\Tenants\EmployeeController::class,'deleteEmployee'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Tenants\EmployeeController::class, 'restoreEmployee'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Tenants\EmployeeController::class, 'realDeleteEmployee'])->name('force-delete');
    });
    //Route::get('/employees', [App\Http\Controllers\Tenants\EmployeeController::class, 'index'])->name('employees.index');

    //Route::get('/employees/fetch', [\App\Http\Controllers\Tenants\EmployeeController::class, 'fetch'])->name('employees.fetch');

    // További bérlői útvonalak...

    /**
     * ========================================
     * ACS SYSTEMS
     * ========================================
     */
    Route::prefix('acs_systems')->name('tenant.acs_systems.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'index'])->name('index');
        Route::post('/fetch', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'fetch'])->name('fetch');
        Route::post('/store', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'storeAcsSystem'])->name('store');
        Route::put('/{id}/update', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'updateAcsSystem'])->name('update');
        Route::delete('/delete', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'deleteAcsSystems'])->name('delete.bulk');
        Route::delete('/{id}', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'deleteAcsSystem'])->name('delete');
        Route::put('/{id}/restore', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'restoreAcsSystem'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Tenants\AcsSystemController::class, 'realDeleteAcsSystem'])->name('force-delete');
    });
    //Route::get('/acs_systems', [App\Http\Controllers\Tenants\AcsSystemController::class, 'index'])->name('acs_system.index');
});
