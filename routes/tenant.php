<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tenant'])->middleware(['auth'])->group(function() {
    
    Route::get('/', fn() => redirect('dashboard') );

    Route::get( '/dashboard', [\App\Http\Controllers\TenantController::class, 'index'] )->name('dashboard');

    Route::get( '/employees', [App\Http\Controllers\Tenants\EmployeeController::class, 'index'] )->name('employees.index');

    // További bérlői útvonalak...

});