<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('command creates an admin user', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Nome', 'Administrador')
        ->expectsQuestion('Email', 'admin@locar.com')
        ->expectsQuestion('Senha', 'Senha@12345')
        ->expectsQuestion('Confirme a senha', 'Senha@12345')
        ->expectsOutput('Admin admin@locar.com criado.')
        ->assertSuccessful();

    $admin = User::where('email', 'admin@locar.com')->first();

    expect($admin->role)->toBe(Role::Admin)
        ->and(Hash::check('Senha@12345', $admin->password))->toBeTrue();
});
