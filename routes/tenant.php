<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['tenant'])->middleware(['auth'])->group(function() {
    Route::get(
        '/', 
        [App\Http\Controllers\TenantController::class, 'index']
    )->name('tenant.index');

Route::get('/dashboard', ['', 'index'])->name('');

    Route::get(
        '/employees', 
        [App\Http\Controllers\Tenants\EmployeeController::class, 'index']
    )->name('employees.index');

    // További bérlői útvonalak...

});