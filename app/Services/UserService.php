<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function ensureDeletable(User $user, User $by): void
    {
        if ($user->is($by) && $user->role === Role::Admin) {
            throw ValidationException::withMessages(['user' => 'Administradores não podem excluir a própria conta.']);
        }

        if ($user->rentals()->exists()) {
            throw ValidationException::withMessages(['user' => 'Usuário possui locações e não pode ser excluído.']);
        }
    }

    public function delete(User $user, User $by): void
    {
        $this->ensureDeletable($user, $by);

        $user->tokens()->delete();
        $user->delete();
    }
}
