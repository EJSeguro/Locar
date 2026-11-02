<?php

namespace Database\Seeders;

use App\Enums\RentalStatus;
use App\Enums\Role;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\RentalService;
use App\Services\VehicleService;
use Carbon\CarbonInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(VehicleService $vehicleService, RentalService $rentalService): void
    {
        $this->user('Administrador', 'admin@locar.com', Role::Admin);
        $this->user('Gestor', 'gestor@locar.com', Role::Gestor);
        $cliente = $this->user('Cliente', 'cliente@locar.com', Role::Cliente);

        $vehicles = collect([
            ['RIO2A24', '001510-8', '2024-1', 'Fiat', 'Mobi Like 1.0 Flex', 2024, 'Flex', 68500],
            ['SPO3B23', '004461-2', '2023-1', 'Chevrolet', 'Onix 1.0 LT Flex', 2023, 'Flex', 82300],
            ['BHZ4C22', '015173-7', '2022-1', 'Hyundai', 'HB20 Comfort 1.0 Flex', 2022, 'Flex', 71900],
            ['CWB5D24', '005536-3', '2024-1', 'Volkswagen', 'T-Cross 200 TSI Flex', 2024, 'Flex', 132400],
            ['POA6E23', '002201-5', '2023-1', 'Toyota', 'Corolla XEi 2.0 Flex', 2023, 'Flex', 148900],
        ])->map(fn (array $vehicle) => Vehicle::firstOrCreate(['plate' => $vehicle[0]], [
            'fipe_code' => $vehicle[1],
            'year_id' => $vehicle[2],
            'brand' => $vehicle[3],
            'model' => $vehicle[4],
            'model_year' => $vehicle[5],
            'fuel' => $vehicle[6],
            'fipe_price' => $vehicle[7],
            'daily_rate' => $vehicleService->dailyRate($vehicle[7]),
        ]));

        if ($cliente->rentals()->exists()) {
            return;
        }

        $this->rental($rentalService, $cliente, $vehicles[0], today()->subDays(20), today()->subDays(15), RentalStatus::Finalizada);
        $this->rental($rentalService, $cliente, $vehicles[1], today()->subDays(10), today()->subDays(8), RentalStatus::Cancelada);
        $this->rental($rentalService, $cliente, $vehicles[3], today()->addDays(3), today()->addDays(7), RentalStatus::Ativa);
    }

    private function user(string $name, string $email, Role $role): User
    {
        return User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => 'password', 'role' => $role]);
    }

    private function rental(RentalService $rentalService, User $user, Vehicle $vehicle, CarbonInterface $start, CarbonInterface $end, RentalStatus $status): void
    {
        Rental::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => $start,
            'end_date' => $end,
            'daily_rate' => $vehicle->daily_rate,
            'total' => $rentalService->total((float) $vehicle->daily_rate, $start, $end),
            'status' => $status,
        ]);
    }
}
