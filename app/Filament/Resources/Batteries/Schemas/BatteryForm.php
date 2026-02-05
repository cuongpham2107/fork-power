<?php

namespace App\Filament\Resources\Batteries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BatteryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin pin')
                    ->description('Nhập thông tin chi tiết về pin')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->schema([
                                TextInput::make('code')
                                    ->label('Mã pin')
                                    ->required(),
                                TextInput::make('type')
                                    ->label('Loại pin'),
                                TextInput::make('capacity')
                                    ->label('Dung lượng'),
                                TextInput::make('voltage')
                                    ->label('Điện áp'),
                                TextInput::make('size')
                                    ->label('Kích thước'),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'standby' => 'Sẵn sàng (Standby)',
                                        'in_use' => 'Đang sử dụng (In Use)',
                                        'charging' => 'Đang nạp (Charging)',
                                        'maintenance' => 'Bảo trì (Maintenance)',
                                    ])
                                    ->default('standby')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
