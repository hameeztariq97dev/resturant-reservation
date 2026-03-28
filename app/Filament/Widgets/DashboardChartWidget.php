<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

abstract class DashboardChartWidget extends ChartWidget
{
    /**
     * Shared canvas height so line + doughnut charts align on the dashboard.
     */
    protected static string $view = 'filament.widgets.chart-widget-fixed';

    protected static ?string $maxHeight = '300px';
}
