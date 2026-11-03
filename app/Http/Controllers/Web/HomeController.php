<?php

namespace App\Http\Controllers\Web;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        return match ($user->role) {
            Role::Admin => to_route('admin.painel'),
            Role::Gestor => to_route('gestor.veiculos'),
            Role::Cliente => to_route('home'),
        };
    }
}
