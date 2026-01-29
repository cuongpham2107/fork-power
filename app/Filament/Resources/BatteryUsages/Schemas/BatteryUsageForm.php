<?php

namespace App\Filament\Resources\BatteryUsages\Schemas;

use App\Models\Battery;
use App\Models\ForkLift;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class BatteryUsageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Thông tin lắp pin')
                        ->description('Nhập thông tin khi lắp pin vào xe nâng')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Grid::make()
                                        ->columns([
                                            'sm' => 1,
                                            'md' => 2,
                                            'xl' => 3,
                                        ])
                                        ->schema([
                                            Select::make('battery_id')
                                                ->label('Pin')
                                                ->options(Battery::all()->pluck('code', 'id'))
                                                ->required()
                                                ->searchable(),
                                            Select::make('fork_lift_id')
                                                ->label('Xe nâng')
                                                ->options(ForkLift::all()->pluck('name', 'id'))
                                                ->required()
                                                ->searchable(),
                                            TextInput::make('charger_bar')
                                                ->label('Số bar máy nạp')
                                                ->numeric(),
                                            TextInput::make('battery_voltage')
                                                ->label('Điện áp pin (V)')
                                                ->numeric(),
                                            TextInput::make('hour_initial')
                                                ->label('Số giờ lắp vào')
                                                ->numeric(),
                                            DateTimePicker::make('installed_at')
                                                ->label('Thời gian lắp'),
                                            Select::make('installed_by')
                                                ->label('Người lắp')
                                                ->options(User::all()->pluck('name', 'id'))
                                                ->searchable(),
                                        ]),
                                ]),
                        ]),
                    Step::make('Thông tin tháo pin')
                        ->description('Nhập thông tin khi tháo pin ra khỏi xe nâng')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Grid::make()
                                        ->columns([
                                            'sm' => 1,
                                            'md' => 2,
                                            'xl' => 3,
                                        ])
                                        ->schema([
                                            TextInput::make('hour_out')
                                                ->label('Số giờ tháo ra')
                                                ->numeric(),
                                            DateTimePicker::make('removed_at')
                                                ->label('Thời gian tháo'),
                                            Select::make('removed_by')
                                                ->label('Người tháo')
                                                ->options(User::all()->pluck('name', 'id'))
                                                ->searchable(),
                                        ]),
                                ]),
                        ]),
                    Step::make('Kết quả')
                        ->description('Tóm tắt và trạng thái sử dụng')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Grid::make()
                                        ->columns([
                                            'sm' => 1,
                                            'md' => 2,
                                        ])
                                        ->schema([
                                            TextInput::make('working_hours')
                                                ->label('Số giờ làm việc')
                                                ->numeric(),
                                            Select::make('status')
                                                ->label('Trạng thái')
                                                ->options([
                                                    'running' => 'Đang chạy',
                                                    'finished' => 'Hoàn thành',
                                                ])
                                                ->default('running')
                                                ->required(),
                                        ]),
                                ]),
                        ]),
                ])
                   ->columnSpanFull(),
            ]);
    }
}
