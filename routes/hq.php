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
     * EMPLOYEES
     * ========================================
     */
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [App\Http\Controllers\Hq\EmployeeController::class, 'index'])->name('index');
        Route::post('/fetch', [App\Http\Controllers\Hq\EmployeeController::class, 'fetch'])->name('fetch');
        Route::post('/store', [App\Http\Controllers\Hq\EmployeeController::class, 'storeEmployee'])->name('store');
        Route::put('/{id}/update', [App\Http\Controllers\Hq\EmployeeController::class, 'updateEmployee'])->name('update');
        Route::delete('/delete', [App\Http\Controllers\Hq\EmployeeController::class, 'deleteEmployees'])->name('delete.bulk');
        Route::delete('/{id}/delete', [App\Http\Controllers\Hq\EmployeeController::class, 'deleteEmployee'])->name('delete');
        Route::put('/{id}/restore', [App\Http\Controllers\Hq\EmployeeController::class, 'restoreEmployee'])->name('restore');
        Route::delete('/{id}/force-delete', [App\Http\Controllers\Hq\EmployeeController::class, 'realDeleteEmployee'])->name('force-delete');
    });
    
    /**
     * ========================================
     * ACS SYSTEMS
     * ========================================
     */
    Route::get(
        '/acs_systems', 
        [App\Http\Controllers\Tenants\AcsSystemController::class, 'index']
    )->name('hq.acs_system.index');
});