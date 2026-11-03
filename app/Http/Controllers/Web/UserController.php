<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Web\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.usuarios', ['usuarios' => User::withCount('rentals')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('admin.cadastrar');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return to_route('admin.usuarios')->with('sucesso', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $usuario): View
    {
        return view('admin.editar', ['usuario' => $usuario]);
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $usuario->update($request->validated());

        return to_route('admin.usuarios')->with('sucesso', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Request $request, User $usuario, UserService $users): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();

        $users->delete($usuario, $admin);

        return to_route('admin.usuarios')->with('sucesso', 'Usuário excluído com sucesso!');
    }
}
