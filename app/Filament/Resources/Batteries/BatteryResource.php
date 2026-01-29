<?php

namespace App\Filament\Resources\Batteries;

use App\Filament\Resources\Batteries\Pages\CreateBattery;
use App\Filament\Resources\Batteries\Pages\EditBattery;
use App\Filament\Resources\Batteries\Pages\ListBatteries;
use App\Filament\Resources\Batteries\Schemas\BatteryForm;
use App\Filament\Resources\Batteries\Tables\BatteriesTable;
use App\Models\Battery;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BatteryResource extends Resource
{
    protected static ?string $model = Battery::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Pin';

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý thiết bị';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return BatteryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BatteriesTable::configure($table);
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
            'index' => ListBatteries::route('/'),
            'create' => CreateBattery::route('/create'),
            'edit' => EditBattery::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 20 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Tổng số pin';
    }
}
