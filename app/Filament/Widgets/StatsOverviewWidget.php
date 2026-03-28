<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Reservations', Reservation::count())
                ->icon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('Confirmed Today', Reservation::query()
                ->where('status', 'confirmed')
                ->whereDate('reservation_date', today())
                ->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total Customers', Customer::count())
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('Total Restaurants', Restaurant::count())
                ->icon('heroicon-o-building-storefront')
                ->color('warning'),
        ];
    }
}
