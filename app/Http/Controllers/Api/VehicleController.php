<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class VehicleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return VehicleResource::collection(Vehicle::latest()->paginate());
    }

    public function store(StoreVehicleRequest $request, VehicleService $vehicles): VehicleResource
    {
        return new VehicleResource($vehicles->create($request->validated()));
    }

    public function show(Vehicle $vehicle): VehicleResource
    {
        return new VehicleResource($vehicle);
    }

    public function destroy(Vehicle $vehicle): Response
    {
        abort_if($vehicle->rentals()->exists(), 422, 'Veículo possui locações. Desabilite-o em vez de excluir.');

        $vehicle->delete();

        return response()->noContent();
    }

    public function availability(Request $request, Vehicle $vehicle): VehicleResource
    {
        $vehicle->update($request->validate(['available' => ['required', 'boolean']]));

        return new VehicleResource($vehicle);
    }
}
