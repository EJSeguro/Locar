<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['plate', 'fipe_code', 'year_id', 'brand', 'model', 'model_year', 'fuel', 'fipe_price', 'daily_rate'])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'model_year' => 'integer',
            'fipe_price' => 'decimal:2',
            'daily_rate' => 'decimal:2',
        ];
    }
}
