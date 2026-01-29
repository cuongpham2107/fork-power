<?php

namespace App\Filament\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

abstract class BaseTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordActions([
                EditAction::make()
                    ->modal()
                    ->button()  
                    ->label('Chỉnh sửa')
                    ->modalHeading('Chỉnh sửa bản ghi')
                    // ->modalWidth('lg')
                    ->modalDescription('Mô tả chi tiết về bản ghi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
