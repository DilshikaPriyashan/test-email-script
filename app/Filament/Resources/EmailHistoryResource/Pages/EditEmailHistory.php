<?php

namespace App\Filament\Resources\EmailHistoryResource\Pages;

use App\Filament\Resources\EmailHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailHistory extends EditRecord
{
    protected static string $resource = EmailHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
