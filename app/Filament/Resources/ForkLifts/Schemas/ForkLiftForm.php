<?php

namespace App\Filament\Resources\ForkLifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ForkLiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin xe nâng')
                    ->description('Nhập thông tin cơ bản về xe nâng')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên xe nâng')
                                    ->required(),
                                TextInput::make('brand')
                                    ->label('Thương hiệu')
                                    ->required(),
                                TextInput::make('model')
                                    ->label('Model')
                                    ->required(),
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'active' => 'Hoạt động',
                                        'inactive' => 'Không hoạt động',
                                        'maintenance' => 'Bảo trì',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
