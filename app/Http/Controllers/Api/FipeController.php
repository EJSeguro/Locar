<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FipeService;
use Illuminate\Http\JsonResponse;

class FipeController extends Controller
{
    public function __construct(private FipeService $fipe) {}

    public function brands(int $type): JsonResponse
    {
        return response()->json($this->fipe->brands($type));
    }

    public function models(int $brand): JsonResponse
    {
        return response()->json($this->fipe->models($brand));
    }

    public function years(string $fipeCode): JsonResponse
    {
        return response()->json($this->fipe->years($fipeCode));
    }
}
