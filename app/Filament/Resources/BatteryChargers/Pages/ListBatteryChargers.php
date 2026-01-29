<?php

namespace App\Filament\Resources\BatteryChargers\Pages;

use App\Filament\Resources\BatteryChargers\BatteryChargerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBatteryChargers extends ListRecords
{
    protected static string $resource = BatteryChargerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
