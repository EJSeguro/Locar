<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FipeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');

        Route::middleware('role:admin')->group(function () {
            Route::apiResource('users', UserController::class)->only(['index', 'store', 'show', 'destroy']);
        });

        Route::middleware('role:admin,gestor')->group(function () {
            Route::get('fipe/brands/{type}', [FipeController::class, 'brands'])->whereIn('type', ['1', '2', '3'])->name('fipe.brands');
            Route::get('fipe/models/{brand}', [FipeController::class, 'models'])->whereNumber('brand')->name('fipe.models');
            Route::get('fipe/years/{fipeCode}', [FipeController::class, 'years'])->where('fipeCode', '\d{6}-\d')->name('fipe.years');

            Route::apiResource('vehicles', VehicleController::class)->only(['index', 'store', 'show', 'destroy']);
        });
    });
});
