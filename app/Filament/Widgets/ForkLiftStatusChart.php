<?php

namespace App\Filament\Widgets;

use App\Models\ForkLift;
use Filament\Widgets\ChartWidget;

class ForkLiftStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tình trạng xe';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $active = ForkLift::where('status', 'active')->count();
        $inactive = ForkLift::where('status', 'inactive')->count();
        $maintenance = ForkLift::where('status', 'maintenance')->count();
        $other = ForkLift::whereNotIn('status', ['active', 'inactive', 'maintenance'])->count();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng xe',
                    'data' => [$active, $inactive, $maintenance, $other],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.7)',  // green - active
                        'rgba(239, 68, 68, 0.7)',  // red - inactive
                        'rgba(245, 158, 11, 0.7)', // amber - maintenance
                        'rgba(156, 163, 175, 0.7)', // gray - other
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Hoạt động', 'Không hoạt động', 'Bảo trì', 'Khác'],
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
