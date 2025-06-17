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
        Route::get('/', [App\Http\Controllers\Tenant\EmployeeController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Tenant\EmployeeController::class, 'store'])->name('store');
        Route::put('/{id}', [App\Http\Controllers\Tenant\EmployeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Tenant\EmployeeController::class, 'destroy'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Tenant\EmployeeController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Tenant\EmployeeController::class, 'forceDelete'])->name('force-delete');
    });
    //Route::get('/employees', [App\Http\Controllers\Tenants\EmployeeController::class, 'index'])->name('employees.index');
    
    //Route::get('/employees/fetch', [\App\Http\Controllers\Tenants\EmployeeController::class, 'fetch'])->name('employees.fetch');

    // További bérlői útvonalak...

    /**
     * ========================================
     * ACS SYSTEMS
     * ========================================
     */
    Route::get('/acs_systems', [App\Http\Controllers\Tenants\AcsSystemController::class, 'index'])->name('acs_system.index');
});