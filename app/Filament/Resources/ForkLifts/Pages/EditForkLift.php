<?php

namespace App\Filament\Resources\ForkLifts\Pages;

use App\Filament\Resources\ForkLifts\ForkLiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForkLift extends EditRecord
{
    protected static string $resource = ForkLiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
