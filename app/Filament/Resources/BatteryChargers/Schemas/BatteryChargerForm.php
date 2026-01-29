<?php

namespace App\Filament\Resources\BatteryChargers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BatteryChargerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin máy sạc')
                    ->description('Nhập thông tin về máy sạc pin')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                TextInput::make('code')
                                    ->label('Mã sạc')
                                    ->required(),
                                TextInput::make('location')
                                    ->label('Vị trí'),
                            ]),
                    ]),
            ]);
    }
}
