<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        /** @var Reservation $record */
        $record = $this->getRecord();
        $record->loadMissing('table.restaurant');

        if ($record->table) {
            $data['filter_restaurant_id'] = $record->table->restaurant_id;
            $data['table_display'] = $record->table->restaurant->name
                .' — Table '.$record->table->table_number
                .' ('.$record->table->capacity.' seats)';
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $filterRestaurantId = filled($data['filter_restaurant_id'] ?? null)
            ? (int) $data['filter_restaurant_id']
            : null;
        unset($data['filter_restaurant_id'], $data['table_display']);

        $partySize = (int) $data['party_size'];
        $reservationDate = Carbon::parse($data['reservation_date']);

        /** @var Reservation $record */
        $record = $this->getRecord();

        $table = app(ReservationService::class)->assignTableForFilament(
            $record,
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
