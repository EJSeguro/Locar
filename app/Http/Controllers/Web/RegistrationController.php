<?php

namespace App\Http\Controllers\Web;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('cadastro.cadastro');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        User::create([...$request->validated(), 'role' => Role::Cliente]);

        return to_route('login')->with('sucesso', 'Cadastro realizado com sucesso! Faça seu login.');
    }
}
