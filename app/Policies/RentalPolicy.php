<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    public function view(User $user, Rental $rental): bool
    {
        return $user->role !== Role::Cliente || $rental->user_id === $user->id;
    }

    public function cancel(User $user, Rental $rental): bool
    {
        return $this->view($user, $rental);
    }
}
