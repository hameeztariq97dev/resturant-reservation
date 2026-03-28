<?php

namespace App\Filament\Widgets;

class BookingMixChartWidget extends DashboardChartWidget
{
    protected static ?string $heading = 'Sample booking mix';

    protected static ?string $description = 'Illustrative status distribution.';

    protected static string $color = 'success';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Share',
                    'data' => [28, 34, 12, 18, 8],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Confirmed', 'Pending', 'Completed', 'Cancelled', 'No-show'],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getOptions(): ?array
    {
        return [
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1600,
                'easing' => 'easeOutQuart',
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '62%',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
