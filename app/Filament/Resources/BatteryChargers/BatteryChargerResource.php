<?php

namespace App\Filament\Resources\BatteryChargers;

use App\Filament\Resources\BatteryChargers\Pages\CreateBatteryCharger;
use App\Filament\Resources\BatteryChargers\Pages\EditBatteryCharger;
use App\Filament\Resources\BatteryChargers\Pages\ListBatteryChargers;
use App\Filament\Resources\BatteryChargers\Schemas\BatteryChargerForm;
use App\Filament\Resources\BatteryChargers\Tables\BatteryChargersTable;
use App\Models\BatteryCharger;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BatteryChargerResource extends Resource
{
    protected static ?string $model = BatteryCharger::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Máy sạc';

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý thiết bị';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return BatteryChargerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BatteryChargersTable::configure($table);
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
            'index' => ListBatteryChargers::route('/'),
            'create' => CreateBatteryCharger::route('/create'),
            'edit' => EditBatteryCharger::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'primary' : 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Tổng số máy sạc';
    }
}
