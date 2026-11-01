<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin', function () {
    $name = text('Nome', required: true, validate: ['name' => 'max:255']);
    $email = text('Email', required: true, validate: ['email' => ['email', 'max:255', Rule::unique(User::class)]]);
    $password = password('Senha', required: true, validate: ['password' => [Password::default()]]);
    password('Confirme a senha', required: true, validate: fn (string $value) => $value === $password ? null : 'As senhas não conferem.');

    User::create(['name' => $name, 'email' => $email, 'password' => $password, 'role' => Role::Admin]);

    $this->info("Admin {$email} criado.");
})->purpose('Cria um usuário administrador');
