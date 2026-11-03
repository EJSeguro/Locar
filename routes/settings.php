<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('minha-conta', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('minha-conta', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('minha-conta', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('minha-conta/senha', [SecurityController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');
});
