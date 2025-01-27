<?php

namespace App\Filament\Resources\APIKeyResource\Pages;

use App\Filament\Resources\APIKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAPIKey extends EditRecord
{
    protected static string $resource = APIKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
