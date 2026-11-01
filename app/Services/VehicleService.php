<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Validation\ValidationException;

class VehicleService
{
    public function __construct(private FipeService $fipe) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Vehicle
    {
        $fipe = $this->fipe->years($data['fipe_code']);
        $year = collect($fipe['years'])->firstWhere('year_id', $data['year_id']);

        if ($year === null) {
            throw ValidationException::withMessages(['year_id' => 'Ano não encontrado para este código FIPE.']);
        }

        return Vehicle::create([
            ...$data,
            'brand' => $fipe['brand'],
            'model' => $fipe['model'],
            'model_year' => $year['model_year'],
            'fuel' => $year['fuel'],
            'fipe_price' => $year['price'],
            'daily_rate' => $this->dailyRate($year['price']),
        ]);
    }

    public function dailyRate(float $fipePrice): float
    {
        return round($fipePrice * 0.0015, 2);
    }
}
