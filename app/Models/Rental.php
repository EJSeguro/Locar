<?php

namespace App\Models;

use App\Enums\RentalStatus;
use Database\Factories\RentalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $vehicle_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $daily_rate
 * @property string $total
 * @property RentalStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'vehicle_id', 'start_date', 'end_date', 'daily_rate', 'total', 'status'])]
class Rental extends Model
{
    /** @use HasFactory<RentalFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'daily_rate' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => RentalStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
