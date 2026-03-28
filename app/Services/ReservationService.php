<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * @param  array{name: string, email: string, phone: string, party_size: int, preferred_date: string, restaurant_id?: int|null}  $data
     */
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                ]
            );

            $preferredDate = Carbon::parse($data['preferred_date'])->startOfDay();

            $table = $this->findAvailableTable(
                (int) $data['party_size'],
                $preferredDate,
                isset($data['restaurant_id']) ? (int) $data['restaurant_id'] : null,
                null
            );

            if ($table === null) {
                throw new \RuntimeException('NO_TABLE_AVAILABLE');
            }

            return Reservation::query()->create([
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'party_size' => (int) $data['party_size'],
                'reservation_date' => $preferredDate,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Pick a table for admin/Filament: keep the current table when it still fits and is free; otherwise assign the next available.
     */
    public function assignTableForFilament(?Reservation $existing, int $partySize, Carbon $reservationDate, ?int $restaurantFilterId): ?RestaurantTable
    {
        $excludeId = $existing?->id;

        if ($existing !== null && $existing->table_id) {
            $existing->loadMissing('table.restaurant');
            $current = $existing->table;

            if ($current && $current->is_active && $current->capacity >= $partySize) {
                if ($restaurantFilterId === null || (int) $current->restaurant_id === (int) $restaurantFilterId) {
                    if (! $this->tableHasBlockingReservation($current, $reservationDate, $excludeId)) {
                        return $current;
                    }
                }
            }
        }

        return $this->findAvailableTable($partySize, $reservationDate, $restaurantFilterId, $excludeId);
    }

    public function findAvailableTable(int $partySize, Carbon $preferredDate, ?int $restaurantId, ?int $excludingReservationId = null): ?RestaurantTable
    {
        $dateString = $preferredDate->toDateString();

        return RestaurantTable::query()
            ->where('is_active', true)
            ->where('capacity', '>=', $partySize)
            ->when($restaurantId !== null, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->whereDoesntHave('reservations', function ($query) use ($dateString, $excludingReservationId) {
                $query->whereDate('reservation_date', $dateString)
                    ->where('status', '!=', 'cancelled');

                if ($excludingReservationId !== null) {
                    $query->where('id', '!=', $excludingReservationId);
                }
            })
            ->orderBy('capacity')
            ->first();
    }

    public function tableHasBlockingReservation(RestaurantTable $table, Carbon $reservationDate, ?int $excludingReservationId): bool
    {
        $dateString = $reservationDate->toDateString();

        return $table->reservations()
            ->whereDate('reservation_date', $dateString)
            ->where('status', '!=', 'cancelled')
            ->when($excludingReservationId !== null, fn ($q) => $q->where('id', '!=', $excludingReservationId))
            ->exists();
    }

    public function cancelReservation(string $code): Reservation
    {
        $reservation = Reservation::query()
            ->where('confirmation_code', $code)
            ->first();

        if ($reservation === null) {
            throw new \RuntimeException('NOT_FOUND');
        }

        if ($reservation->status === 'cancelled') {
            throw new \RuntimeException('ALREADY_CANCELLED');
        }

        $reservation->update(['status' => 'cancelled']);

        return $reservation->fresh();
    }
}
