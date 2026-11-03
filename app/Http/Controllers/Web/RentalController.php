<?php

namespace App\Http\Controllers\Web;

use App\Enums\RentalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRentalRequest;
use App\Models\Rental;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RentalController extends Controller
{
    public function mine(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('cliente.locacoes', ['locacoes' => $user->rentals()->with('vehicle')->latest()->get()]);
    }

    public function store(StoreRentalRequest $request, RentalService $rentals): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{vehicle_id: int, start_date: string, end_date: string} $data */
        $data = $request->validated();

        $rentals->create($user, $data);

        return to_route('cliente.locacoes')->with('sucesso', 'Locação realizada com sucesso!');
    }

    public function cancel(Request $request, Rental $locacao, RentalService $rentals): RedirectResponse
    {
        Gate::authorize('cancel', $locacao);

        /** @var User $user */
        $user = $request->user();

        $rentals->changeStatus($locacao, RentalStatus::Cancelada, $user);

        return to_route('cliente.locacoes')->with('sucesso', 'Locação cancelada com sucesso!');
    }

    public function index(): View
    {
        return view('gestor.locacoes', ['locacoes' => Rental::with(['user', 'vehicle'])->latest()->get()]);
    }

    public function finish(Rental $locacao, RentalService $rentals): RedirectResponse
    {
        $rentals->changeStatus($locacao, RentalStatus::Finalizada);

        return to_route('gestor.locacoes')->with('sucesso', 'Locação finalizada com sucesso!');
    }
}
