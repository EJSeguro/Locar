<?php

namespace App\Http\Requests\Web;

use App\Concerns\ProfileValidationRules;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $usuario */
        $usuario = $this->route('usuario');

        $roles = match (true) {
            $usuario->is($this->user()) => [Role::Admin],
            $usuario->role === Role::Cliente => [Role::Cliente],
            default => [Role::Admin, Role::Gestor],
        };

        return [
            ...$this->profileRules($usuario->id),
            'role' => ['required', Rule::enum(Role::class)->only($roles)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.enum' => 'Perfil não permitido para este usuário.',
        ];
    }
}
