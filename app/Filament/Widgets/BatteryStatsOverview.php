<?php

namespace App\Filament\Widgets;

use App\Models\Battery;
use App\Models\BatteryUsage;
use App\Models\ForkLift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BatteryStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|array|null $columns = 5;

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        // Lượt sử dụng pin trong tháng hiện tại
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyUsages = BatteryUsage::whereYear('installed_at', $currentYear)
            ->whereMonth('installed_at', $currentMonth)
            ->count();

        // Lượt sử dụng pin trong quý hiện tại
        $currentQuarter = (int) ceil($currentMonth / 3);
        $quarterStart = now()->startOfQuarter();
        $quarterEnd = now()->endOfQuarter();

        $quarterlyUsages = BatteryUsage::whereBetween('installed_at', [$quarterStart, $quarterEnd])
            ->count();

        // Lượt sử dụng pin trong năm hiện tại
        $yearlyUsages = BatteryUsage::whereYear('installed_at', $currentYear)
            ->count();

        // Tình trạng pin
        $totalBatteries = Battery::count();
        $activeBatteries = Battery::where('status', 'active')->count();
        $standbyBatteries = Battery::where('status', 'standby')->count();
        $maintenanceBatteries = Battery::where('status', 'maintenance')->count();

        // Tình trạng xe
        $totalForkLifts = ForkLift::count();
        $activeForkLifts = ForkLift::where('status', 'active')->count();
        $inactiveForkLifts = ForkLift::where('status', '!=', 'active')->count();

        return [
            Stat::make('Lượt sử dụng pin - Tháng '.$currentMonth, $monthlyUsages)
                ->description('Tháng '.$currentMonth.'/'.$currentYear)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Lượt sử dụng pin - Quý '.$currentQuarter, $quarterlyUsages)
                ->description('Quý '.$currentQuarter.'/'.$currentYear)
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Lượt sử dụng pin - Năm '.$currentYear, $yearlyUsages)
                ->description('Cả năm '.$currentYear)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Tổng pin', $totalBatteries)
                ->description("Kích hoạt: {$activeBatteries} | Sẵn sàng: {$standbyBatteries} | Bảo trì: {$maintenanceBatteries}")
                ->descriptionIcon('heroicon-m-battery-100')
                ->color($activeBatteries > 0 ? 'success' : 'warning'),

            Stat::make('Tổng xe', $totalForkLifts)
                ->description("Hoạt động: {$activeForkLifts} | Không hoạt động: {$inactiveForkLifts}")
                ->descriptionIcon('heroicon-m-truck')
                ->color($activeForkLifts > 0 ? 'success' : 'danger'),
        ];
    }
}
