<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
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
            'created_at' => $this->created_at,
        ];
    }
}
