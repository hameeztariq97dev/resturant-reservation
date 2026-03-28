<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Services\ReservationService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $filterRestaurantId = filled($data['filter_restaurant_id'] ?? null)
            ? (int) $data['filter_restaurant_id']
            : null;
        unset($data['filter_restaurant_id']);

        $partySize = (int) $data['party_size'];
        $reservationDate = Carbon::parse($data['reservation_date']);

        $table = app(ReservationService::class)->assignTableForFilament(
            null,
            $partySize,
            $reservationDate,
            $filterRestaurantId
        );

        if ($table === null) {
            Notification::make()
                ->title('No available table')
                ->body('No available table found for the requested party size and date.')
                ->danger()
                ->send();

            throw new Halt;
        }

        $data['table_id'] = $table->id;

        return $data;
    }
}
