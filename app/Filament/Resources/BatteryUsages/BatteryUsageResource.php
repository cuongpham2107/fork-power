<?php

namespace App\Filament\Resources\BatteryUsages;

use App\Filament\Resources\BatteryUsages\Pages\CreateBatteryUsage;
use App\Filament\Resources\BatteryUsages\Pages\EditBatteryUsage;
use App\Filament\Resources\BatteryUsages\Pages\ListBatteryUsages;
use App\Filament\Resources\BatteryUsages\Schemas\BatteryUsageForm;
use App\Filament\Resources\BatteryUsages\Tables\BatteryUsagesTable;
use App\Models\BatteryUsage;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BatteryUsageResource extends Resource
{
    protected static ?string $model = BatteryUsage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Sử dụng pin';

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý thiết bị';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'charger_bar';

    public static function form(Schema $schema): Schema
    {
        return BatteryUsageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BatteryUsagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBatteryUsages::route('/'),
            'create' => CreateBatteryUsage::route('/create'),
            'edit' => EditBatteryUsage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'running')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'running')->count();

        return $count > 0 ? 'warning' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Số bản ghi đang chạy';
    }
}
