<?php

namespace App\Filament\Resources\EmailHistoryResource\Pages;

use App\Filament\Resources\EmailHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailHistories extends ListRecords
{
    protected static string $resource = EmailHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
