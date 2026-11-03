<?php

use App\Enums\RentalStatus;
use App\Models\Rental;
use App\Models\Vehicle;
use App\Services\RentalService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    if (DB::getDriverName() !== 'pgsql') {
        $this->markTestSkipped('Objetos do banco existem apenas no PostgreSQL.');
    }
});

test('fn_calcular_total_locacao multiplies daily rate by days', function () {
    expect((float) DB::scalar("select fn_calcular_total_locacao(106.03, '2026-11-10', '2026-11-13')"))->toBe(318.09);
});

test('fn_veiculo_disponivel considers availability and overlapping rentals', function () {
    $rental = Rental::factory()->create(['start_date' => '2026-12-10', 'end_date' => '2026-12-15']);
    $disabled = Vehicle::factory()->create(['available' => false]);

    $available = fn (int $id, string $start, string $end) => DB::scalar('select fn_veiculo_disponivel(?, ?, ?)', [$id, $start, $end]);

    expect($available($rental->vehicle_id, '2026-12-12', '2026-12-20'))->toBeFalse()
        ->and($available($rental->vehicle_id, '2026-12-15', '2026-12-20'))->toBeTrue()
        ->and($available($disabled->id, '2026-12-01', '2026-12-02'))->toBeFalse();
});

test('trigger calculates rental total on insert', function () {
    $rental = Rental::factory()->create(['daily_rate' => 100, 'total' => 0, 'start_date' => '2026-12-01', 'end_date' => '2026-12-05']);

    expect($rental->fresh()->total)->toBe('400.00');
});

test('trigger blocks overlapping active rental', function () {
    $rental = Rental::factory()->create(['start_date' => '2026-12-10', 'end_date' => '2026-12-15']);

    Rental::factory()->create(['vehicle_id' => $rental->vehicle_id, 'start_date' => '2026-12-12', 'end_date' => '2026-12-14']);
})->throws(QueryException::class, 'indisponível');

test('procedures finish and cancel active rentals', function () {
    $finish = Rental::factory()->create();
    $cancel = Rental::factory()->create();

    app(RentalService::class)->changeStatus($finish, RentalStatus::Finalizada);
    app(RentalService::class)->changeStatus($cancel, RentalStatus::Cancelada);

    expect($finish->fresh()->status)->toBe(RentalStatus::Finalizada)
        ->and($cancel->fresh()->status)->toBe(RentalStatus::Cancelada);

    DB::statement('call sp_finalizar_locacao(?)', [$finish->id]);
})->throws(QueryException::class, 'não está ativa');
