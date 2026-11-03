<?php

use App\Enums\Role;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('redirecionar'))->assertRedirect(route('login'));
});

test('users are redirected to their area after login', function (Role $role, string $route) {
    $this->actingAs(User::factory()->role($role)->create())
        ->get(route('redirecionar'))
        ->assertRedirect(route($route));
})->with([
    [Role::Admin, 'admin.painel'],
    [Role::Gestor, 'gestor.veiculos'],
    [Role::Cliente, 'home'],
]);
