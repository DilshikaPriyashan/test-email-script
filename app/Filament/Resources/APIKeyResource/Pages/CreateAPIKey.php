<?php

namespace App\Filament\Resources\APIKeyResource\Pages;

use App\Filament\Resources\APIKeyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAPIKey extends CreateRecord
{
    protected static string $resource = APIKeyResource::class;

    protected static bool $canCreateAnother = false;
}
