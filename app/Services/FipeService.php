<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class FipeService
{
    /**
     * @return array<int, array{id: int, brand: string}>
     */
    public function brands(int $type): array
    {
        return $this->get("brands/{$type}")->json();
    }

    /**
     * @return array<int, array{id: int, fipe_code: string, model: string, years: string}>
     */
    public function models(int $brandId): array
    {
        return $this->get("models/{$brandId}")->json();
    }

    /**
     * @return array{brand: string, model: string, reference: string, years: array<int, array{year_id: string, model_year: string, fuel: string, price: float}>}
     */
    public function years(string $fipeCode): array
    {
        return $this->get("years/{$fipeCode}")->json();
    }

    private function get(string $path): Response
    {
        $response = Http::baseUrl('https://api.invertexto.com/v1/fipe')
            ->timeout(10)
            ->get($path, ['token' => config('services.invertexto.token')]);

        if (in_array($response->status(), [404, 422], true)) {
            throw ValidationException::withMessages(['fipe_code' => 'Veículo não encontrado na tabela FIPE.']);
        }

        abort_if($response->failed(), 502, 'Serviço FIPE indisponível.');

        return $response;
    }
}
