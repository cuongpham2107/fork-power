<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BatteryStatsOverview;
use App\Filament\Widgets\BatteryStatusChart;
use App\Filament\Widgets\BatteryUsageChart;
use App\Filament\Widgets\ForkLiftStatusChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            BatteryStatsOverview::class,
            BatteryUsageChart::class,
            BatteryStatusChart::class,
            ForkLiftStatusChart::class,
            AccountWidget::class,
            FilamentInfoWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
