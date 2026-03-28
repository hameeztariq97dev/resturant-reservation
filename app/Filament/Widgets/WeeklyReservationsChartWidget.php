<?php

namespace App\Filament\Widgets;

class WeeklyReservationsChartWidget extends DashboardChartWidget
{
    protected static ?string $heading = '7-day reservation trend';

    protected static ?string $description = 'Sample activity — illustrative data for the dashboard.';

    protected static string $color = 'primary';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'New bookings',
                    'data' => [14, 22, 18, 28, 24, 32, 36],
                    'fill' => true,
                    'tension' => 0.42,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Guest arrivals',
                    'data' => [10, 16, 14, 22, 20, 26, 30],
                    'fill' => false,
                    'tension' => 0.42,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getOptions(): ?array
    {
        return [
            'animation' => [
                'duration' => 1800,
                'easing' => 'easeOutQuart',
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
