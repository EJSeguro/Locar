<?php

namespace Database\Factories;

use App\Enums\RentalStatus;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'start_date' => today()->addDay(),
            'end_date' => today()->addDays(4),
            'daily_rate' => 100,
            'total' => 300,
            'status' => RentalStatus::Ativa,
        ];
    }
}
