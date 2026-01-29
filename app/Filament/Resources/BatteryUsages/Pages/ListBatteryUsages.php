<?php

namespace App\Filament\Resources\BatteryUsages\Pages;

use App\Filament\Resources\BatteryUsages\BatteryUsageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBatteryUsages extends ListRecords
{
    protected static string $resource = BatteryUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
