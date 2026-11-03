<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\RegistrationController;
use App\Http\Controllers\Web\RentalController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VehicleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('veiculos', [VehicleController::class, 'index'])->name('veiculos');
Route::get('veiculos/{vehicle}', [VehicleController::class, 'show'])->name('veiculos.detalhes');

Route::middleware('guest')->group(function () {
    Route::get('cadastro', [RegistrationController::class, 'create'])->name('cadastro');
    Route::post('cadastro', [RegistrationController::class, 'store'])->name('cadastro.salvar');
});

Route::get('redirecionar', [HomeController::class, 'redirect'])->middleware('auth')->name('redirecionar');

require __DIR__.'/settings.php';

Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('minhas-locacoes', [RentalController::class, 'mine'])->name('cliente.locacoes');
    Route::post('locacoes', [RentalController::class, 'store'])->name('cliente.locacoes.salvar');
    Route::patch('minhas-locacoes/{locacao}/cancelar', [RentalController::class, 'cancel'])->name('cliente.locacoes.cancelar');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/', 'admin.painel')->name('painel');
    Route::get('usuarios', [UserController::class, 'index'])->name('usuarios');
    Route::get('usuarios/cadastrar', [UserController::class, 'create'])->name('usuarios.cadastrar');
    Route::post('usuarios', [UserController::class, 'store'])->name('usuarios.salvar');
    Route::get('usuarios/{usuario}/editar', [UserController::class, 'edit'])->name('usuarios.editar');
    Route::put('usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.atualizar');
    Route::delete('usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.excluir');
});

Route::prefix('gestor')->name('gestor.')->middleware(['auth', 'role:admin,gestor'])->group(function () {
    Route::get('veiculos', [VehicleController::class, 'manage'])->name('veiculos');
    Route::get('veiculos/cadastrar', [VehicleController::class, 'create'])->name('veiculos.cadastrar');
    Route::post('veiculos', [VehicleController::class, 'store'])->name('veiculos.salvar');
    Route::get('veiculos/{vehicle}/editar', [VehicleController::class, 'details'])->name('veiculos.editar');
    Route::put('veiculos/{vehicle}', [VehicleController::class, 'update'])->name('veiculos.atualizar');
    Route::delete('veiculos/{vehicle}', [VehicleController::class, 'destroy'])->name('veiculos.excluir');
    Route::patch('veiculos/{vehicle}/disponibilidade', [VehicleController::class, 'availability'])->name('veiculos.disponibilidade');
    Route::get('locacoes', [RentalController::class, 'index'])->name('locacoes');
    Route::patch('locacoes/{locacao}/finalizar', [RentalController::class, 'finish'])->name('locacoes.finalizar');
});
