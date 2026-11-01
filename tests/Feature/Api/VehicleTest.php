<?php

use App\Enums\Role;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Http::fake([
        'api.invertexto.com/v1/fipe/years/001420-6*' => Http::response([
            'brand' => 'Fiat',
            'model' => '500 Cabrio Dualogic Flex 1.4 8V',
            'reference' => 'outubro de 2026',
            'years' => [
                ['year_id' => '2017-5', 'model_year' => '2017', 'fuel' => 'Flex', 'price' => 70687],
            ],
        ]),
        'api.invertexto.com/*' => Http::response(['message' => 'not found'], 422),
    ]);
});

test('gestor registers a vehicle with daily rate of 0.15% of fipe', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());

    $this->postJson('/api/v1/vehicles', ['plate' => 'abc-1d23', 'fipe_code' => '001420-6', 'year_id' => '2017-5'])
        ->assertCreated()
        ->assertJsonPath('data.plate', 'ABC1D23')
        ->assertJsonPath('data.brand', 'Fiat')
        ->assertJsonPath('data.model_year', 2017)
        ->assertJsonPath('data.fipe_price', '70687.00')
        ->assertJsonPath('data.daily_rate', '106.03');
});

test('vehicle with unknown fipe year is rejected', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());

    $this->postJson('/api/v1/vehicles', ['plate' => 'ABC1D23', 'fipe_code' => '001420-6', 'year_id' => '1999-1'])
        ->assertJsonValidationErrors('year_id');
});

test('vehicle with unknown fipe code is rejected', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());

    $this->postJson('/api/v1/vehicles', ['plate' => 'ABC1D23', 'fipe_code' => '999999-9', 'year_id' => '2017-5'])
        ->assertJsonValidationErrors('fipe_code');
});

test('duplicated plate is rejected', function () {
    Sanctum::actingAs(User::factory()->role(Role::Gestor)->create());
    Vehicle::factory()->create(['plate' => 'ABC1D23']);

    $this->postJson('/api/v1/vehicles', ['plate' => 'ABC1D23', 'fipe_code' => '001420-6', 'year_id' => '2017-5'])
        ->assertJsonValidationErrors('plate');
});

test('admin removes a vehicle', function () {
    Sanctum::actingAs(User::factory()->role(Role::Admin)->create());
    $vehicle = Vehicle::factory()->create();

    $this->deleteJson("/api/v1/vehicles/{$vehicle->id}")->assertNoContent();

    expect(Vehicle::find($vehicle->id))->toBeNull();
});

test('cliente cannot manage vehicles', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/vehicles', [])->assertForbidden();
    $this->deleteJson('/api/v1/vehicles/'.Vehicle::factory()->create()->id)->assertForbidden();
});
