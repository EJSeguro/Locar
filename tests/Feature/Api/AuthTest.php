<?php

use App\Models\User;

test('login returns a token for valid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'role']]);
});

test('login rejects wrong password', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('protected routes require a token', function () {
    $this->getJson('/api/v1/me')->assertUnauthorized();
});
