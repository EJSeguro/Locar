<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('plate')) {
            $this->merge(['plate' => strtoupper(str_replace('-', '', (string) $this->input('plate')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plate' => ['sometimes', 'required', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', Rule::unique('vehicles', 'plate')->ignore($this->route('vehicle'))],
            'daily_rate' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'available' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
