<?php

use Illuminate\Support\Facades\Route;

Route::domain('hq.mt')->middleware(['auth'])->group(function() {
    Route::get(
        '/', 
        [App\Http\Controllers\hq\AdminController::class, 'index']
    )->name('admin.index');

    // Tovobbi admin útvonalak...
});