<?php

namespace App\Filament\Resources\BatteryUsages\Pages;

use App\Filament\Resources\BatteryUsages\BatteryUsageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBatteryUsage extends EditRecord
{
    protected static string $resource = BatteryUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
