<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('admin creates a gestor', function () {
    Sanctum::actingAs(User::factory()->role(Role::Admin)->create());

    $this->postJson('/api/v1/users', [
        'name' => 'Gestor',
        'email' => 'gestor@locar.com',
        'password' => 'Senha@12345',
        'password_confirmation' => 'Senha@12345',
        'role' => 'gestor',
    ])->assertCreated()->assertJsonPath('data.role', 'gestor');

    expect(User::where('email', 'gestor@locar.com')->first()->role)->toBe(Role::Gestor);
});

test('admin cannot create a cliente through this route', function () {
    Sanctum::actingAs(User::factory()->role(Role::Admin)->create());

    $this->postJson('/api/v1/users', [
        'name' => 'Cliente',
        'email' => 'cliente@locar.com',
        'password' => 'Senha@12345',
        'password_confirmation' => 'Senha@12345',
        'role' => 'cliente',
    ])->assertJsonValidationErrors('role');
});

test('gestor and cliente cannot manage users', function (Role $role) {
    Sanctum::actingAs(User::factory()->role($role)->create());

    $this->getJson('/api/v1/users')->assertForbidden();
})->with([Role::Gestor, Role::Cliente]);

test('admin removes a user but not himself', function () {
    $admin = User::factory()->role(Role::Admin)->create();
    $gestor = User::factory()->role(Role::Gestor)->create();
    Sanctum::actingAs($admin);

    $this->deleteJson("/api/v1/users/{$gestor->id}")->assertNoContent();
    $this->deleteJson("/api/v1/users/{$admin->id}")->assertUnprocessable();

    expect(User::find($gestor->id))->toBeNull()
        ->and(User::find($admin->id))->not->toBeNull();
});
