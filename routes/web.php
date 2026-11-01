<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::get('/teste-blade', function () {
    return view('home');
});

route::get('/veiculos', function () {
    return view('cliente.veiculos');
})->name('veiculos');


Route::get('/teste-detalhes', function () {
    $veiculo = (object) [
        'brand' => 'Chevrolet',
        'model' => 'Onix',
        'model_year' => 2024,
        'fuel' => 'Flex',
        'daily_rate' => 120.00
    ];

    return view('cliente.detalhes', compact('veiculo'));
});

route::get('/teste-login', function () {
    return view('login.login');
});

route::get('/teste-cadastro', function () {
    return view('cadastro.cadastro');
});