<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tenant'])->middleware(['auth'])->group(function() {
    
    Route::get('/', fn() => redirect('dashboard') );

    Route::get( '/dashboard', [\App\Http\Controllers\TenantController::class, 'index'] )->name('dashboard');

    /**
     * ========================================
     * EMPLOYEES
     * ========================================
     */
    Route::get(
        '/employees', 
        [App\Http\Controllers\Tenants\EmployeeController::class, 'index']
    )->name('employees.index');

    // További bérlői útvonalak...

    /**
     * ========================================
     * ACS SYSTEMS
     * ========================================
     */
    Route::get(
        '/acs_systems', 
        [App\Http\Controllers\Tenants\AcsSystemController::class, 'index']
    )->name('acs_system.index');
});