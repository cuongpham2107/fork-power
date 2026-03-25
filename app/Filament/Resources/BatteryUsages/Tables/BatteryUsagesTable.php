<?php

namespace App\Filament\Resources\BatteryUsages\Tables;

use App\Filament\Tables\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BatteryUsagesTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->columns([
                // Thông tin chính
                TextColumn::make('battery.code')
                    ->label('Mã pin')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã pin')
                    ->copyMessageDuration(1500),

                TextColumn::make('forkLift.name')
                    ->label('Tên xe nâng')
                    ->searchable()
                    ->sortable(),

                // Thông số kỹ thuật
                TextColumn::make('charger_bar')
                    ->label('Vạch máy nạp')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('screen_bar')
                    ->label('Vạch màn hình')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    }),

                // Số giờ
                TextColumn::make('hour_initial')
                    ->label('Giờ lắp vào')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1)),

                TextColumn::make('hour_out')
                    ->label('Giờ tháo ra')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1)),

                TextColumn::make('working_hours')
                    ->label('Giờ làm việc')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1))
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'info',
                    }),

                // Thời gian
                TextColumn::make('installed_at')
                    ->label('Thời gian lắp')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('removed_at')
                    ->label('Thời gian tháo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Nhân viên
                TextColumn::make('installedBy.name')
                    ->label('Người lắp')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('removedBy.name')
                    ->label('Người tháo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Trạng thái
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
                    ->searchable()
                    ->sortable(),

                // Hệ thống
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'finished' => 'Hoàn thành',
                        'running' => 'Đang chạy',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                SelectFilter::make('battery_id')
                    ->label('Pin')
                    ->relationship('battery', 'code')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tất cả pin'),

                SelectFilter::make('forklift_id')
                    ->label('Xe nâng')
                    ->relationship('forkLift', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tất cả xe nâng'),

                TernaryFilter::make('has_working_hours')
                    ->label('Có giờ làm việc')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('working_hours')->where('working_hours', '>', 0),
                        false: fn (Builder $query) => $query->whereNull('working_hours')->orWhere('working_hours', '<=', 0),
                    ),

                // Filter theo khoảng thời gian
                SelectFilter::make('installed_date_range')
                    ->label('Thời gian lắp')
                    ->options([
                        'today' => 'Hôm nay',
                        'yesterday' => 'Hôm qua',
                        'last_7_days' => '7 ngày qua',
                        'last_30_days' => '30 ngày qua',
                        'this_month' => 'Tháng này',
                        'last_month' => 'Tháng trước',
                    ])
                    ->placeholder('Tất cả thời gian')
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'today' => $query->whereDate('installed_at', today()),
                            'yesterday' => $query->whereDate('installed_at', yesterday()),
                            'last_7_days' => $query->where('installed_at', '>=', now()->subDays(7)),
                            'last_30_days' => $query->where('installed_at', '>=', now()->subDays(30)),
                            'this_month' => $query->whereMonth('installed_at', now()->month)->whereYear('installed_at', now()->year),
                            'last_month' => $query->whereMonth('installed_at', now()->subMonth()->month)->whereYear('installed_at', now()->subMonth()->year),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('installed_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->searchable();
    }
}
