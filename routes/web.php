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

route::get('/teste-locacoes', function () {
    return view('cliente.locacoes');
});


Route::get('/teste-gestor-veiculos', function () {
    $veiculos = [
        (object) [
            'id' => 1,
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'model_year' => 2024,
            'daily_rate' => 120.00
        ]
    ];

    return view('gestor.veiculos', compact('veiculos'));
});


route::get('/teste-gestor-cadastrar', function () {
    return view('gestor.cadastrar');
});


Route::get('/teste-gestor-editar', function () {
    $veiculo = (object) [
        'plate' => 'ABC1D23',
        'brand' => 'Chevrolet',
        'model' => 'Onix',
        'model_year' => 2024,
        'fuel' => 'Flex',
        'fipe_code' => '004001-0',
        'year_id' => '2024-1',
        'fipe_price' => 75000.00,
        'daily_rate' => 120.00
    ];

    return view('gestor.editar', compact('veiculo'));
});

route::get('/teste-gestor-locacoes', function () {
    return view('gestor.locacoes');
});

route::get('/teste-admin', function () {
    return view('admin.painel');
});

route::get('/teste-admin-usuarios', function () {
    return view('admin.usuarios');
});