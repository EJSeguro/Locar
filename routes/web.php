<?php

use App\Enums\Role;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\StoreVehicleRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Pagina inicial

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';


Route::get('/veiculos', function () {
    $veiculos = Vehicle::orderBy('id', 'desc')->get();

    return view('cliente.veiculos', compact('veiculos'));
})->name('veiculos');

Route::get('/veiculos/{vehicle}', function (Vehicle $vehicle) {
    return view('cliente.detalhes', [
        'veiculo' => $vehicle
    ]);
})->name('veiculos.detalhes');

Route::get('/cadastro', function () {
    return view('cadastro.cadastro');
})->middleware('guest')->name('cadastro');

Route::post('/cadastro', function (Request $request) {
    $dados = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', Password::default(), 'confirmed']
    ]);

    $dados['role'] = Role::Cliente;

    User::create($dados);

    return redirect('/login')
        ->with('sucesso', 'Cadastro realizado com sucesso! Faça seu login.');
})->middleware('guest')->name('cadastro.salvar');

// Redirecionamento depois do login

Route::get('/redirecionar', function () {
    $usuario = auth()->user();

    if ($usuario->role === Role::Admin) {
        return redirect()->route('admin.painel');
    }

    if ($usuario->role === Role::Gestor) {
        return redirect()->route('gestor.veiculos');
    }

    if ($usuario->role === Role::Cliente) {
        return redirect()->route('home');
    }

    abort(403);
})->middleware('auth')->name('redirecionar');

// cliente

Route::middleware('auth')->group(function () {

    Route::get('/minhas-locacoes', function () {
        if (auth()->user()->role !== Role::Cliente) {
            abort(403);
        }

        return view('cliente.locacoes');
    })->name('cliente.locacoes');

});

// adm

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/', function () {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        return view('admin.painel');
    })->name('painel');

    Route::get('/usuarios', function () {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        $usuarios = User::orderBy('id')->get();

        return view('admin.usuarios', compact('usuarios'));
    })->name('usuarios');

    Route::get('/usuarios/cadastrar', function () {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        return view('admin.cadastrar');
    })->name('usuarios.cadastrar');

    Route::post('/usuarios', function (StoreUserRequest $request) {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        User::create($request->validated());

        return redirect()->route('admin.usuarios')
            ->with('sucesso', 'Usuário cadastrado com sucesso!');
    })->name('usuarios.salvar');

    Route::get('/usuarios/{usuario}/editar', function (User $usuario) {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        return view('admin.editar', compact('usuario'));
    })->name('usuarios.editar');

    Route::put('/usuarios/{usuario}', function (Request $request, User $usuario) {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id)
            ],
            'role' => ['required', Rule::enum(Role::class)]
        ]);

        if (
            $usuario->id === auth()->id() &&
            $dados['role'] !== Role::Admin->value
        ) {
            return back()->withErrors([
                'role' => 'Você não pode alterar seu próprio perfil de administrador.'
            ])->withInput();
        }

        $usuario->update($dados);

        return redirect()->route('admin.usuarios')
            ->with('sucesso', 'Usuário atualizado com sucesso!');
    })->name('usuarios.atualizar');

    Route::delete('/usuarios/{usuario}', function (User $usuario) {
        if (auth()->user()->role !== Role::Admin) {
            abort(403);
        }

        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios')
                ->with('erro', 'Você não pode excluir sua própria conta.');
        }

        $usuario->tokens()->delete();
        $usuario->delete();

        return redirect()->route('admin.usuarios')
            ->with('sucesso', 'Usuário excluído com sucesso!');
    })->name('usuarios.excluir');

});

// Area do gestor e administrador

Route::prefix('gestor')->name('gestor.')->middleware('auth')->group(function () {

    Route::get('/veiculos', function () {
        if (
            auth()->user()->role !== Role::Gestor &&
            auth()->user()->role !== Role::Admin
        ) {
            abort(403);
        }

        $veiculos = Vehicle::orderBy('id', 'desc')->get();

        return view('gestor.veiculos', compact('veiculos'));
    })->name('veiculos');

    Route::get('/veiculos/cadastrar', function () {
        if (
            auth()->user()->role !== Role::Gestor &&
            auth()->user()->role !== Role::Admin
        ) {
            abort(403);
        }

        return view('gestor.cadastrar');
    })->name('veiculos.cadastrar');

    Route::post('/veiculos', function (
        StoreVehicleRequest $request,
        VehicleService $vehicles
    ) {
        if (
            auth()->user()->role !== Role::Gestor &&
            auth()->user()->role !== Role::Admin
        ) {
            abort(403);
        }

        $vehicles->create($request->validated());

        return redirect()->route('gestor.veiculos')
            ->with('sucesso', 'Veículo cadastrado com sucesso!');
    })->name('veiculos.salvar');

    Route::get('/veiculos/{vehicle}/editar', function (Vehicle $vehicle) {
        if (
            auth()->user()->role !== Role::Gestor &&
            auth()->user()->role !== Role::Admin
        ) {
            abort(403);
        }

        return view('gestor.editar', [
            'veiculo' => $vehicle
        ]);
    })->name('veiculos.editar');

    Route::get('/locacoes', function () {
        if (
            auth()->user()->role !== Role::Gestor &&
            auth()->user()->role !== Role::Admin
        ) {
            abort(403);
        }

        return view('gestor.locacoes');
    })->name('locacoes');

});