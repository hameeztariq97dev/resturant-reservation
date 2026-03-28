<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize as InfolistTextEntrySize;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['customer', 'table.restaurant']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Guest')
                    ->description('Select the customer for this booking.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->getOptionLabelFromRecordUsing(fn (Customer $record): string => "{$record->name} ({$record->email})"),
                    ])
                    ->columns(1),

                Section::make('Restaurant & table')
                    ->description('Optionally limit which venue is considered. On save, the system picks the smallest active table with enough seats that is free on that date (non-cancelled reservations block the slot).')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Forms\Components\Select::make('filter_restaurant_id')
                            ->label('Restaurant')
                            ->placeholder('All restaurants')
                            ->options(fn (): array => Restaurant::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->nullable()
                            ->native(false)
                            ->hint('Optional')
                            ->hintIcon('heroicon-m-information-circle'),
                        Forms\Components\TextInput::make('table_display')
                            ->label('Assigned table')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-o-table-cells')
                            ->prefixIconColor('primary')
                            ->visibleOn('edit'),
                    ])
                    ->columns(1)
                    ->compact(),

                Section::make('When & how many')
                    ->description('Party size, date and time, and booking status.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'lg' => 3,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('party_size')
                                    ->label('Party size')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->suffix('guests')
                                    ->prefixIcon('heroicon-o-user-group'),
                                Forms\Components\DateTimePicker::make('reservation_date')
                                    ->label('Reservation date & time')
                                    ->required()
                                    ->timezone('Asia/Karachi')
                                    ->native(false)
                                    ->seconds(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'cancelled' => 'Cancelled',
                                        'completed' => 'Completed',
                                    ])
                                    ->prefixIcon('heroicon-o-flag'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Notes')
                    ->description('Optional details for staff.')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Internal notes')
                            ->placeholder('Special requests, occasion, accessibility…')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->label('Code')
                    ->copyable()
                    ->copyMessage('Code copied')
                    ->copyMessageDuration(1500)
                    ->badge()
                    ->size(TextColumnSize::Small)
                    ->color('gray')
                    ->fontFamily(FontFamily::Mono)
                    ->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->weight(FontWeight::Medium)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('table.restaurant.name')
                    ->label('Restaurant')
                    ->badge()
                    ->size(TextColumnSize::Small)
                    ->color('primary')
                    ->icon('heroicon-o-building-storefront')
                    ->iconPosition(IconPosition::Before),
                Tables\Columns\TextColumn::make('table.table_number')
                    ->label('Table')
                    ->formatStateUsing(fn ($state): string => $state !== null && $state !== '' ? (string) $state : '—')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('party_size')
                    ->label('Guests')
                    ->badge()
                    ->size(TextColumnSize::Small)
                    ->color('gray')
                    ->formatStateUsing(fn ($state): string => $state !== null ? (string) $state.' guests' : '—'),
                Tables\Columns\TextColumn::make('reservation_date')
                    ->label('Date & time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->size(TextColumnSize::Small)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\Filter::make('reservation_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['from'] ?? null),
                                fn (Builder $q): Builder => $q->whereDate('reservation_date', '>=', $data['from']),
                            )
                            ->when(
                                filled($data['until'] ?? null),
                                fn (Builder $q): Builder => $q->whereDate('reservation_date', '<=', $data['until']),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->modalHeading(fn (Reservation $record): string => __('Booking').' · '.$record->confirmation_code)
                    ->modalDescription(fn (Reservation $record): string => $record->table?->restaurant?->name
                        ? (string) $record->table->restaurant->name
                        : __('Reservation details'))
                    ->infolist([
                        InfolistSection::make(__('Overview'))
                            ->description(__('Confirmation code, status, and schedule'))
                            ->icon('heroicon-o-sparkles')
                            ->iconColor('primary')
                            ->schema([
                                InfolistGrid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
                                    ->schema([
                                        TextEntry::make('confirmation_code')
                                            ->label(__('Confirmation code'))
                                            ->copyable()
                                            ->copyMessage(__('Copied'))
                                            ->badge()
                                            ->color('gray')
                                            ->fontFamily(FontFamily::Mono)
                                            ->weight(FontWeight::SemiBold)
                                            ->size(InfolistTextEntrySize::Large),
                                        TextEntry::make('status')
                                            ->label(__('Status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger',
                                                'completed' => 'gray',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('party_size')
                                            ->label(__('Party size'))
                                            ->icon('heroicon-o-user-group')
                                            ->formatStateUsing(fn ($state): string => $state !== null ? (string) $state.' '.__('guests') : '—'),
                                        TextEntry::make('reservation_date')
                                            ->label(__('Date & time'))
                                            ->icon('heroicon-o-clock')
                                            ->dateTime(null, 'Asia/Karachi'),
                                    ]),
                            ]),
                        InfolistSection::make(__('Guest'))
                            ->description(__('Contact on file'))
                            ->icon('heroicon-o-user')
                            ->iconColor('gray')
                            ->schema([
                                InfolistGrid::make(['default' => 1, 'md' => 3])
                                    ->schema([
                                        TextEntry::make('customer.name')
                                            ->label(__('Name'))
                                            ->weight(FontWeight::Medium),
                                        TextEntry::make('customer.email')
                                            ->label(__('Email'))
                                            ->icon('heroicon-m-envelope')
                                            ->copyable(),
                                        TextEntry::make('customer.phone')
                                            ->label(__('Phone'))
                                            ->icon('heroicon-m-phone')
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        InfolistSection::make(__('Venue'))
                            ->description(__('Where they are seated'))
                            ->icon('heroicon-o-building-storefront')
                            ->iconColor('primary')
                            ->schema([
                                InfolistGrid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextEntry::make('table.restaurant.name')
                                            ->label(__('Restaurant'))
                                            ->weight(FontWeight::Medium),
                                        TextEntry::make('table.table_number')
                                            ->label(__('Table'))
                                            ->formatStateUsing(fn ($state): string => $state !== null && $state !== ''
                                                ? __('Table :num', ['num' => $state])
                                                : '—'),
                                    ]),
                            ]),
                        InfolistSection::make(__('Staff notes'))
                            ->description(__('Internal only'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('gray')
                            ->collapsed(fn (?Reservation $record): bool => blank($record?->notes))
                            ->schema([
                                TextEntry::make('notes')
                                    ->label('')
                                    ->placeholder(__('No notes added'))
                                    ->columnSpanFull(),
                            ]),
                        InfolistSection::make(__('Record'))
                            ->description(__('Timestamps'))
                            ->icon('heroicon-o-calendar')
                            ->iconColor('gray')
                            ->collapsed()
                            ->schema([
                                InfolistGrid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label(__('Created'))
                                            ->dateTime(),
                                        TextEntry::make('updated_at')
                                            ->label(__('Last updated'))
                                            ->dateTime(),
                                    ]),
                            ]),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_selected')
                        ->label('Confirm selected')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'confirmed']);
                        }),
                    Tables\Actions\BulkAction::make('cancel_selected')
                        ->label('Cancel selected')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'cancelled']);
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
