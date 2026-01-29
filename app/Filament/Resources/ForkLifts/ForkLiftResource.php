<?php

namespace App\Filament\Resources\ForkLifts;

use App\Filament\Resources\ForkLifts\Pages\CreateForkLift;
use App\Filament\Resources\ForkLifts\Pages\EditForkLift;
use App\Filament\Resources\ForkLifts\Pages\ListForkLifts;
use App\Filament\Resources\ForkLifts\Schemas\ForkLiftForm;
use App\Filament\Resources\ForkLifts\Tables\ForkLiftsTable;
use App\Models\ForkLift;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForkLiftResource extends Resource
{
    protected static ?string $model = ForkLift::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Xe nâng';

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý thiết bị';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ForkLiftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForkLiftsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 5 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Tổng số xe nâng';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForkLifts::route('/'),
            'create' => CreateForkLift::route('/create'),
            'edit' => EditForkLift::route('/{record}/edit'),
        ];
    }
}
