<?php

namespace App\Filament\Widgets;

use App\Models\Battery;
use Filament\Widgets\ChartWidget;

class BatteryStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tình trạng pin';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $active = Battery::where('status', 'active')->count();
        $standby = Battery::where('status', 'standby')->count();
        $maintenance = Battery::where('status', 'maintenance')->count();
        $other = Battery::whereNotIn('status', ['active', 'standby', 'maintenance'])->count();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng pin',
                    'data' => [$active, $standby, $maintenance, $other],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.7)',  // green - active
                        'rgba(59, 130, 246, 0.7)', // blue - standby
                        'rgba(245, 158, 11, 0.7)', // amber - maintenance
                        'rgba(156, 163, 175, 0.7)', // gray - other
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Hoạt động', 'Sẵn sàng', 'Bảo trì', 'Khác'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
        ];
    }
}
