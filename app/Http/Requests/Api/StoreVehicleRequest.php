<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['plate' => strtoupper(str_replace('-', '', (string) $this->input('plate')))]);
    }

    public function rules(): array
    {
        return [
            'plate' => ['required', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', 'unique:vehicles,plate'],
            'fipe_code' => ['required', 'string', 'regex:/^\d{6}-\d$/'],
            'year_id' => ['required', 'string', 'regex:/^\d{4,5}-\d$/'],
        ];
    }
}
