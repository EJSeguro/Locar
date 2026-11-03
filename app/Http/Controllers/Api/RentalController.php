<?php

namespace App\Http\Controllers\Api;

use App\Enums\RentalStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class RentalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $rentals = Rental::with(['user', 'vehicle'])
            ->when($user->role === Role::Cliente, fn ($query) => $query->whereBelongsTo($user))
            ->latest()
            ->paginate();

        return RentalResource::collection($rentals);
    }

    public function store(StoreRentalRequest $request, RentalService $rentals): RentalResource
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{vehicle_id: int, start_date: string, end_date: string} $data */
        $data = $request->validated();

        return new RentalResource($rentals->create($user, $data)->load('vehicle'));
    }

    public function show(Rental $rental): RentalResource
    {
        Gate::authorize('view', $rental);

        return new RentalResource($rental->load(['user', 'vehicle']));
    }

    public function cancel(Request $request, Rental $rental, RentalService $rentals): RentalResource
    {
        Gate::authorize('cancel', $rental);

        return new RentalResource($rentals->changeStatus($rental, RentalStatus::Cancelada, $request->user()));
    }

    public function finish(Rental $rental, RentalService $rentals): RentalResource
    {
        return new RentalResource($rentals->changeStatus($rental, RentalStatus::Finalizada));
    }
}
