<?php

namespace App\Filament\Resources\Batteries\Tables;

use App\Filament\Tables\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BatteriesTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->columns([
                TextColumn::make('code')
                    ->label('Mã pin')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Loại pin')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Dung lượng')
                    ->searchable(),
                TextColumn::make('voltage')
                    ->label('Điện áp')
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Kích thước')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'standby' => 'success',
                        'in_use' => 'info',
                        'charging' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'standby' => 'Sẵn sàng',
                        'in_use' => 'Đang dùng',
                        'charging' => 'Đang nạp',
                        'maintenance' => 'Bảo trì',
                        default => $state,
                    }),
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
