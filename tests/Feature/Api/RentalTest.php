<?php

use App\Enums\RentalStatus;
use App\Enums\Role;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Laravel\Sanctum\Sanctum;

test('cliente rents a vehicle and pays daily rate times days', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create(['daily_rate' => 106.03]);

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->addDay()->toDateString(),
        'end_date' => today()->addDays(4)->toDateString(),
    ])
        ->assertCreated()
        ->assertJsonPath('data.daily_rate', '106.03')
        ->assertJsonPath('data.total', '318.09')
        ->assertJsonPath('data.status', 'ativa');
});

test('vehicle cannot be rented in an overlapping period', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();
    Rental::factory()->for($vehicle)->create([
        'start_date' => today()->addDays(2),
        'end_date' => today()->addDays(5),
    ]);

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->addDays(4)->toDateString(),
        'end_date' => today()->addDays(6)->toDateString(),
    ])->assertJsonValidationErrors('vehicle_id');
});

test('vehicle can be rented from the day a previous rental ends or after a cancellation', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();
    Rental::factory()->for($vehicle)->create([
        'start_date' => today()->addDay(),
        'end_date' => today()->addDays(3),
    ]);
    Rental::factory()->for($vehicle)->create([
        'start_date' => today()->addDays(3),
        'end_date' => today()->addDays(6),
        'status' => RentalStatus::Cancelada,
    ]);

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->addDays(3)->toDateString(),
        'end_date' => today()->addDays(5)->toDateString(),
    ])->assertCreated();
});

test('vehicle with an overdue active rental cannot be rented until it is finished', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();
    $overdue = Rental::factory()->for($vehicle)->create([
        'start_date' => today()->subDays(5),
        'end_date' => today()->subDay(),
    ]);
    $payload = [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->addDays(10)->toDateString(),
        'end_date' => today()->addDays(12)->toDateString(),
    ];

    $this->postJson('/api/v1/rentals', $payload)->assertJsonValidationErrors('vehicle_id');

    $overdue->update(['status' => RentalStatus::Finalizada]);

    $this->postJson('/api/v1/rentals', $payload)->assertCreated();
});

test('vehicle can be rented from the end date of a rental that ends today', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();
    Rental::factory()->for($vehicle)->create([
        'start_date' => today()->subDays(2),
        'end_date' => today(),
    ]);

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->toDateString(),
        'end_date' => today()->addDays(2)->toDateString(),
    ])->assertCreated();
});

test('disabled vehicle cannot be rented', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create(['available' => false]);

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->addDay()->toDateString(),
        'end_date' => today()->addDays(2)->toDateString(),
    ])->assertJsonValidationErrors('vehicle_id');
});

test('gestor disables a vehicle', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());
    $vehicle = Vehicle::factory()->create();

    $this->patchJson("/api/v1/vehicles/{$vehicle->id}/availability", ['available' => false])
        ->assertJsonPath('data.available', false);
});

test('vehicle and user with rentals cannot be deleted', function () {
    Sanctum::actingAs(User::factory()->role(Role::Admin)->create());
    $rental = Rental::factory()->create();

    $this->deleteJson("/api/v1/vehicles/{$rental->vehicle_id}")->assertStatus(422);
    $this->deleteJson("/api/v1/users/{$rental->user_id}")->assertStatus(422);

    expect(Vehicle::find($rental->vehicle_id))->not->toBeNull()
        ->and(User::find($rental->user_id))->not->toBeNull();
});

test('rental dates must be in the future and end after start', function () {
    Sanctum::actingAs(User::factory()->create());
    $vehicle = Vehicle::factory()->create();

    $this->postJson('/api/v1/rentals', [
        'vehicle_id' => $vehicle->id,
        'start_date' => today()->subDay()->toDateString(),
        'end_date' => today()->subDay()->toDateString(),
    ])->assertJsonValidationErrors(['start_date', 'end_date']);
});

test('only cliente can create rentals', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());

    $this->postJson('/api/v1/rentals', [])->assertForbidden();
});

test('cliente sees only own rentals and gestor sees all', function () {
    $cliente = User::factory()->create();
    Rental::factory()->for($cliente)->create();
    Rental::factory()->create();

    Sanctum::actingAs($cliente);
    $this->getJson('/api/v1/rentals')->assertJsonCount(1, 'data');

    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());
    $this->getJson('/api/v1/rentals')->assertJsonCount(2, 'data');
});

test('cliente cancels own rental but not others', function () {
    $cliente = User::factory()->create();
    $own = Rental::factory()->for($cliente)->create();
    $other = Rental::factory()->create();

    Sanctum::actingAs($cliente);

    $this->patchJson("/api/v1/rentals/{$own->id}/cancel")->assertJsonPath('data.status', 'cancelada');
    $this->patchJson("/api/v1/rentals/{$other->id}/cancel")->assertForbidden();
    $this->getJson("/api/v1/rentals/{$other->id}")->assertForbidden();
});

test('cliente cannot cancel from the start date on, gestor can', function () {
    $cliente = User::factory()->create();
    $today = Rental::factory()->for($cliente)->create(['start_date' => today(), 'end_date' => today()->addDays(2)]);
    $ongoing = Rental::factory()->for($cliente)->create(['start_date' => today()->subDay(), 'end_date' => today()->addDay()]);

    Sanctum::actingAs($cliente);
    $this->patchJson("/api/v1/rentals/{$today->id}/cancel")->assertJsonValidationErrors('status');
    $this->patchJson("/api/v1/rentals/{$ongoing->id}/cancel")->assertJsonValidationErrors('status');

    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());
    $this->patchJson("/api/v1/rentals/{$ongoing->id}/cancel")->assertJsonPath('data.status', 'cancelada');
});

test('gestor finishes an active rental only once', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());
    $rental = Rental::factory()->create();

    $this->patchJson("/api/v1/rentals/{$rental->id}/finish")->assertJsonPath('data.status', 'finalizada');
    $this->patchJson("/api/v1/rentals/{$rental->id}/finish")->assertJsonValidationErrors('status');
});

test('cliente cannot finish rentals', function () {
    $cliente = User::factory()->create();
    Sanctum::actingAs($cliente);

    $this->patchJson('/api/v1/rentals/'.Rental::factory()->for($cliente)->create()->id.'/finish')->assertForbidden();
});
