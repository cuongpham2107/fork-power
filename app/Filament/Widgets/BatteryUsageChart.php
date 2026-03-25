<?php

namespace App\Filament\Widgets;

use App\Models\BatteryUsage;
use Filament\Widgets\ChartWidget;

class BatteryUsageChart extends ChartWidget
{
    protected ?string $heading = 'Thống kê lượt sử dụng pin theo thời gian';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $currentYear = now()->year;
        $monthlyData = [];

        // Lấy data 12 tháng gần nhất
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $count = BatteryUsage::whereYear('installed_at', $year)
                ->whereMonth('installed_at', $month)
                ->count();

            $monthlyData[] = [
                'month' => $date->format('M/Y'),
                'count' => $count,
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Lượt sử dụng pin',
                    'data' => array_column($monthlyData, 'count'),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_column($monthlyData, 'month'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
