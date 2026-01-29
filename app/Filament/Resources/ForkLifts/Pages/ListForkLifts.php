<?php

namespace App\Filament\Resources\ForkLifts\Pages;

use App\Filament\Resources\ForkLifts\ForkLiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForkLifts extends ListRecords
{
    protected static string $resource = ForkLiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
