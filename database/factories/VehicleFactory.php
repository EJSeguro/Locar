<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->numberBetween(30000, 200000);

        return [
            'plate' => strtoupper(fake()->unique()->bothify('???#?##')),
            'fipe_code' => fake()->numerify('######-#'),
            'year_id' => '2020-1',
            'brand' => fake()->company(),
            'model' => fake()->word(),
            'model_year' => 2020,
            'fuel' => 'Flex',
            'fipe_price' => $price,
            'daily_rate' => round($price * 0.0015, 2),
        ];
    }
}
