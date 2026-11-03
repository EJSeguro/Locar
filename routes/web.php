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
use App\Http\Requests\Api\StoreRentalRequest;
use App\Services\RentalService;

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


Route::middleware(['auth', 'role:cliente'])->group(function () {

    Route::get('/minhas-locacoes', function () {
        $locacoes = auth()->user()
            ->rentals()
            ->with('vehicle')
            ->latest()
            ->get();
        return view('cliente.locacoes', compact('locacoes'));
    })->name('cliente.locacoes');

    Route::post('/locacoes', function (
        StoreRentalRequest $request,
        RentalService $rentals
    ) {
        $rentals->create($request->user(), $request->validated());

        return redirect()->route('cliente.locacoes')
            ->with('sucesso', 'Locação realizada com sucesso!');

    })->name('cliente.locacoes.salvar');

});


Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/', function () {
            return view('admin.painel');
        })->name('painel');

        Route::get('/usuarios', function () {
            $usuarios = User::orderBy('id')->get();

            return view('admin.usuarios', compact('usuarios'));
        })->name('usuarios');

        Route::get('/usuarios/cadastrar', function () {
            return view('admin.cadastrar');
        })->name('usuarios.cadastrar');

        Route::post('/usuarios', function (StoreUserRequest $request) {
            User::create($request->validated());

            return redirect()->route('admin.usuarios')
                ->with('sucesso', 'Usuário cadastrado com sucesso!');
        })->name('usuarios.salvar');

        Route::get('/usuarios/{usuario}/editar', function (User $usuario) {
            return view('admin.editar', compact('usuario'));
        })->name('usuarios.editar');

        Route::put('/usuarios/{usuario}', function (Request $request, User $usuario) {
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

// gestor e admin

Route::prefix('gestor')
    ->name('gestor.')
    ->middleware(['auth', 'role:admin,gestor'])
    ->group(function () {

        Route::get('/veiculos', function () {
            $veiculos = Vehicle::orderBy('id', 'desc')->get();

            return view('gestor.veiculos', compact('veiculos'));
        })->name('veiculos');

        Route::get('/veiculos/cadastrar', function () {
            return view('gestor.cadastrar');
        })->name('veiculos.cadastrar');

        Route::post('/veiculos', function (
            StoreVehicleRequest $request,
            VehicleService $vehicles
        ) {
            $vehicles->create($request->validated());

            return redirect()->route('gestor.veiculos')
                ->with('sucesso', 'Veículo cadastrado com sucesso!');
        })->name('veiculos.salvar');

        Route::get('/veiculos/{vehicle}/editar', function (Vehicle $vehicle) {
            return view('gestor.editar', [
                'veiculo' => $vehicle
            ]);
        })->name('veiculos.editar');

        Route::get('/locacoes', function () {
            return view('gestor.locacoes');
        })->name('locacoes');

    });