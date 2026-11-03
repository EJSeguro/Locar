<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVehicleRequest;
use App\Http\Requests\Api\UpdateVehicleAvailabilityRequest;
use App\Http\Requests\Api\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('cliente.veiculos', ['veiculos' => Vehicle::where('available', true)->latest('id')->get()]);
    }

    public function show(Vehicle $vehicle): View
    {
        abort_unless($vehicle->available, 404);

        return view('cliente.detalhes', ['veiculo' => $vehicle]);
    }

    public function manage(): View
    {
        return view('gestor.veiculos', ['veiculos' => Vehicle::latest('id')->get()]);
    }

    public function create(): View
    {
        return view('gestor.cadastrar');
    }

    public function store(StoreVehicleRequest $request, VehicleService $vehicles): RedirectResponse
    {
        $vehicles->create($request->validated());

        return to_route('gestor.veiculos')->with('sucesso', 'Veículo cadastrado com sucesso!');
    }

    public function details(Vehicle $vehicle): View
    {
        return view('gestor.editar', ['veiculo' => $vehicle]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return to_route('gestor.veiculos')->with('sucesso', 'Veículo atualizado com sucesso!');
    }

    public function destroy(Vehicle $vehicle, VehicleService $vehicles): RedirectResponse
    {
        $vehicles->delete($vehicle);

        return to_route('gestor.veiculos')->with('sucesso', 'Veículo excluído com sucesso!');
    }

    public function availability(UpdateVehicleAvailabilityRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return to_route('gestor.veiculos')->with('sucesso', 'Disponibilidade do veículo atualizada com sucesso!');
    }
}
