<?php

namespace App\Filament\Resources\BatteryChargers\Pages;

use App\Filament\Resources\BatteryChargers\BatteryChargerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBatteryCharger extends EditRecord
{
    protected static string $resource = BatteryChargerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
