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
     * ACS SYSTEMS
     * ========================================
     */
    Route::get(
        '/acs_systems', 
        [App\Http\Controllers\Tenants\AcsSystemController::class, 'index']
    )->name('hq.acs_system.index');
});