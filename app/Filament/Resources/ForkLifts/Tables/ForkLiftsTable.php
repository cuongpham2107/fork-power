<?php

namespace App\Filament\Resources\ForkLifts\Tables;

use App\Filament\Tables\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ForkLiftsTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->columns([
                TextColumn::make('name')
                    ->label('Tên')
                    ->searchable(),
                TextColumn::make('brand')
                    ->label('Thương hiệu')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->label('Số serial')
                    ->searchable(),
                TextColumn::make('total_working_hours')
                    ->label('Tổng giờ hoạt động')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hoạt động',
                        'inactive' => 'Không hoạt động',
                        'maintenance' => 'Bảo trì',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'maintenance' => 'warning',
                        default => 'info',
                    })
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
