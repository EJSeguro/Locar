<?php

namespace App\Http\Resources;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vehicle
 */
class VehicleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plate' => $this->plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'model_year' => $this->model_year,
            'fuel' => $this->fuel,
            'fipe_code' => $this->fipe_code,
            'fipe_price' => $this->fipe_price,
            'daily_rate' => $this->daily_rate,
            'available' => $this->available,
            'created_at' => $this->created_at,
        ];
    }
}
