<?php

namespace App\Filament\Resources\BatteryUsages\Tables;

use App\Filament\Tables\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BatteryUsagesTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->columns([
                TextColumn::make('battery.code')
                    ->label('Mã pin')
                    ->sortable(),
                TextColumn::make('forkLift.name')
                    ->label('Tên xe nâng')
                    ->sortable(),
                TextColumn::make('charger_bar')
                    ->label('Số vạch hiển thị trên máy nạp')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('screen_bar')
                    ->label('Số vạch hiển thị trên màn hình')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hour_initial')
                    ->label('Số giờ lắp vào')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('installed_at')
                    ->label('Thời gian lắp')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('hour_out')
                    ->label('Số giờ tháo ra')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('removed_at')
                    ->label('Thời gian tháo')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('working_hours')
                    ->label('Số giờ làm việc')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('installedBy.name')
                    ->label('Người lắp')
                    ->sortable(),
                TextColumn::make('removedBy.name')
                    ->label('Người tháo')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'finished' => 'Hoàn thành',
                        'running' => 'Đang chạy',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'finished' => 'success',
                        'running' => 'warning',
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
