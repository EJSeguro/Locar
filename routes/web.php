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
    if (auth()->user()->role !== \App\Enums\Role::Cliente) {
        abort(403);
    }
    return view('cliente.locacoes');
})->middleware('auth');


Route::get('/teste-gestor-veiculos', function () {
    if (auth()->user()->role !== \App\Enums\Role::Gestor) {
        abort(403);
    }
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
    if (auth()->user()->role !== \App\Enums\Role::Gestor) {
        abort(403);
    }
    return view('gestor.cadastrar');
});


Route::get('/teste-gestor-editar', function () {
    if (auth()->user()->role !== \App\Enums\Role::Gestor) {
        abort(403);
    }
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
    if (auth()->user()->role !== \App\Enums\Role::Gestor) {
        abort(403);
    }
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


Route::delete('/teste-admin-usuarios/{usuario}', function (\App\Models\User $usuario) {
    if (auth()->user()->role !== \App\Enums\Role::Admin) {
        abort(403);
    }
    if ($usuario->id === auth()->id()) {
        return redirect('/teste-admin-usuarios')
            ->with('erro', 'Você não pode excluir sua própria conta.');
    }
    $usuario->tokens()->delete();
    $usuario->delete();
    return redirect('/teste-admin-usuarios')
        ->with('sucesso', 'Usuário excluído com sucesso!');
})->middleware('auth');


Route::get('/teste-admin-editar-usuario/{usuario}', function (\App\Models\User $usuario) {
    if (auth()->user()->role !== \App\Enums\Role::Admin) {
        abort(403);
    }
    return view('admin.editar', compact('usuario'));
})->middleware('auth');


Route::put('/teste-admin-editar-usuario/{usuario}', function (
    \Illuminate\Http\Request $request,
    \App\Models\User $usuario
) {

    abort_unless(
        auth()->user()->role === \App\Enums\Role::Admin,
        403
    );

    $dados = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            \Illuminate\Validation\Rule::unique('users', 'email')
                ->ignore($usuario->id),
        ],
        'role' => [
            'required',
            \Illuminate\Validation\Rule::enum(\App\Enums\Role::class),
        ],
    ]);

    if (
        $usuario->id === auth()->id()
        && $dados['role'] !== \App\Enums\Role::Admin->value
    ) {
        return back()->withErrors([
            'role' => 'Você não pode alterar seu próprio perfil de administrador.',
        ])->withInput();
    }
    $usuario->update($dados);
    return redirect('/teste-admin-usuarios')
        ->with('sucesso', 'Usuário atualizado com sucesso!');
})->middleware('auth');


Route::post('/cadastrar-cliente', function (\Illuminate\Http\Request $request) {

    if (auth()->check()) {
        return redirect('/');
    }

    $dados = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => [
            'required',
            'string',
            \Illuminate\Validation\Rules\Password::default(),
            'confirmed'
        ],
    ]);


    $dados['role'] = \App\Enums\Role::Cliente;

    \App\Models\User::create($dados);

    return redirect('/login')
        ->with('sucesso', 'Cadastro realizado com sucesso! Faça seu login.');

})->middleware('guest');
