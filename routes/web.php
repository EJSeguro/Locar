<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

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
    if (auth()->user()->role !== \App\Enums\Role::Admin) {
        abort(403);
    }
    return view('admin.painel');
})->middleware('auth');


Route::get('/teste-admin-usuarios', function () {
    if (auth()->user()->role !== \App\Enums\Role::Admin) {
        abort(403);
    }
    $usuarios = \App\Models\User::orderBy('id')->get();
    return view('admin.usuarios', compact('usuarios'));
})->middleware('auth');

Route::get('/redirecionar', function () {
    $usuario = auth()->user();
    if ($usuario->role === \App\Enums\Role::Admin) {
        return redirect('/teste-admin');
    }
    if ($usuario->role === \App\Enums\Role::Gestor) {
        return redirect('/teste-gestor-veiculos');
    }
    if ($usuario->role === \App\Enums\Role::Cliente) {
        return redirect('/teste-blade');
    }
    abort(403);
})->middleware('auth');

route::get('/teste-admin-cadastrar-usuario', function () {
    if (auth()->user()->role !== \App\Enums\Role::Admin) {
        abort(403);
    }
    return view('admin.cadastrar');
})->middleware('auth');


Route::post('/teste-admin-cadastrar-usuario', function (\App\Http\Requests\Api\StoreUserRequest $request) {
    abort_unless(
        auth()->user()?->role === \App\Enums\Role::Admin,
        403
    );
    \App\Models\User::create($request->validated());
    return redirect('/teste-admin-usuarios')
        ->with('sucesso', 'Usuário cadastrado com sucesso!');
})->middleware('auth');
