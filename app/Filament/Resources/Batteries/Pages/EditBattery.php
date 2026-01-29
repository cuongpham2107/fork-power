<?php

namespace App\Filament\Resources\Batteries\Pages;

use App\Filament\Resources\Batteries\BatteryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBattery extends EditRecord
{
    protected static string $resource = BatteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
