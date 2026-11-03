<?php

use App\Enums\RentalStatus;
use App\Enums\Role;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;

test('visitor registers as cliente', function () {
    $this->post(route('cadastro.salvar'), [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('login'));

    expect(User::firstWhere('email', 'ana@example.com')->role)->toBe(Role::Cliente);
});

test('public list hides disabled vehicles', function () {
    $available = Vehicle::factory()->create(['model' => 'ModeloDisponivel']);
    $disabled = Vehicle::factory()->create(['model' => 'ModeloDesabilitado', 'available' => false]);

    $this->get(route('veiculos'))->assertSee($available->model)->assertDontSee($disabled->model);
    $this->get(route('veiculos.detalhes', $disabled))->assertNotFound();
});

test('gestor toggles vehicle availability', function () {
    $vehicle = Vehicle::factory()->create();

    $this->actingAs(User::factory()->role(Role::Gestor)->create())
        ->patch(route('gestor.veiculos.disponibilidade', $vehicle), ['available' => 0])
        ->assertRedirect(route('gestor.veiculos'));

    expect($vehicle->fresh()->available)->toBeFalse();
});

test('admin edits users within allowed roles', function () {
    $admin = User::factory()->role(Role::Admin)->create();
    $cliente = User::factory()->create();
    $gestor = User::factory()->role(Role::Gestor)->create();
    $this->actingAs($admin);

    $this->put(route('admin.usuarios.atualizar', $cliente), ['name' => 'Novo', 'email' => $cliente->email, 'role' => 'cliente'])
        ->assertSessionHasNoErrors();
    expect($cliente->fresh()->name)->toBe('Novo');

    $this->put(route('admin.usuarios.atualizar', $cliente), ['name' => 'X', 'email' => $cliente->email, 'role' => 'admin'])
        ->assertSessionHasErrors('role');
    $this->put(route('admin.usuarios.atualizar', $gestor), ['name' => 'X', 'email' => $gestor->email, 'role' => 'cliente'])
        ->assertSessionHasErrors('role');
    $this->put(route('admin.usuarios.atualizar', $admin), ['name' => 'X', 'email' => $admin->email, 'role' => 'gestor'])
        ->assertSessionHasErrors('role');
});

test('admin cannot delete user with rentals', function () {
    $rental = Rental::factory()->create();

    $this->actingAs(User::factory()->role(Role::Admin)->create())
        ->delete(route('admin.usuarios.excluir', $rental->user))
        ->assertSessionHasErrors('user');

    expect($rental->user->fresh())->not->toBeNull();
});

test('cliente cancels only own rentals', function () {
    $cliente = User::factory()->create();
    $own = Rental::factory()->for($cliente)->create();
    $other = Rental::factory()->create();
    $this->actingAs($cliente);

    $this->patch(route('cliente.locacoes.cancelar', $own))->assertSessionHasNoErrors();
    $this->patch(route('cliente.locacoes.cancelar', $other))->assertForbidden();

    expect($own->fresh()->status)->toBe(RentalStatus::Cancelada);
});

test('gestor lists and finishes rentals', function () {
    $rental = Rental::factory()->create();
    $this->actingAs(User::factory()->role(Role::Gestor)->create());

    $this->get(route('gestor.locacoes'))->assertSee($rental->user->name);
    $this->patch(route('gestor.locacoes.finalizar', $rental))->assertRedirect(route('gestor.locacoes'));

    expect($rental->fresh()->status)->toBe(RentalStatus::Finalizada);
});

test('gestor edits a vehicle', function () {
    $vehicle = Vehicle::factory()->create();

    $this->actingAs(User::factory()->role(Role::Gestor)->create())
        ->put(route('gestor.veiculos.atualizar', $vehicle), ['plate' => 'XYZ9A87', 'daily_rate' => 99.9])
        ->assertRedirect(route('gestor.veiculos'));

    expect($vehicle->fresh())->plate->toBe('XYZ9A87')->daily_rate->toBe('99.90');
});

test('gestor deletes only vehicles without rentals', function () {
    $vehicle = Vehicle::factory()->create();
    $rented = Rental::factory()->create()->vehicle;
    $this->actingAs(User::factory()->role(Role::Gestor)->create());

    $this->delete(route('gestor.veiculos.excluir', $vehicle))->assertRedirect(route('gestor.veiculos'));
    $this->delete(route('gestor.veiculos.excluir', $rented))->assertSessionHasErrors('vehicle');

    expect(Vehicle::find($vehicle->id))->toBeNull()
        ->and($rented->fresh())->not->toBeNull();
});
