<?php

namespace App\Services;

use App\Enums\RentalStatus;
use App\Enums\Role;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalService
{
    /**
     * @param  array{vehicle_id: int, start_date: string, end_date: string}  $data
     */
    public function create(User $user, array $data): Rental
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        return DB::transaction(function () use ($user, $data, $start, $end) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($data['vehicle_id']);

            if (! $vehicle->available) {
                throw ValidationException::withMessages(['vehicle_id' => 'Veículo indisponível para locação.']);
            }

            $isOverdue = Rental::where('vehicle_id', $vehicle->id)
                ->where('status', RentalStatus::Ativa)
                ->whereDate('end_date', '<', today())
                ->exists();

            if ($isOverdue) {
                throw ValidationException::withMessages(['vehicle_id' => 'Veículo aguardando devolução de uma locação anterior.']);
            }

            $isBooked = Rental::where('vehicle_id', $vehicle->id)
                ->where('status', RentalStatus::Ativa)
                ->whereDate('start_date', '<', $end)
                ->whereDate('end_date', '>', $start)
                ->exists();

            if ($isBooked) {
                throw ValidationException::withMessages(['vehicle_id' => 'Veículo já reservado neste período.']);
            }

            return Rental::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'start_date' => $start,
                'end_date' => $end,
                'daily_rate' => $vehicle->daily_rate,
                'total' => $this->total((float) $vehicle->daily_rate, $start, $end),
                'status' => RentalStatus::Ativa,
            ]);
        });
    }

    public function total(float $dailyRate, CarbonInterface $start, CarbonInterface $end): float
    {
        return round($dailyRate * $start->diffInDays($end), 2);
    }

    public function changeStatus(Rental $rental, RentalStatus $status, ?User $by = null): Rental
    {
        if ($rental->status !== RentalStatus::Ativa) {
            throw ValidationException::withMessages(['status' => 'Só locações ativas podem ser alteradas.']);
        }

        if ($status === RentalStatus::Cancelada && $by?->role === Role::Cliente && $rental->start_date->lte(today())) {
            throw ValidationException::withMessages(['status' => 'Só é possível cancelar antes da data de retirada.']);
        }

        $rental->update(['status' => $status]);

        return $rental;
    }
}
