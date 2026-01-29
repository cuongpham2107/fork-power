<?php

namespace App\Filament\Resources\BatteryChargers\Tables;

use App\Filament\Tables\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BatteryChargersTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->columns([
                TextColumn::make('code')
                    ->label('Mã sạc')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Vị trí')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ]);
    }
}
