<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Models\EmailHistory;
use App\Models\EmailTemplate;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class ViewEmailHistory extends Page
{
    protected static string $resource = EmailTemplateResource::class;

    protected static string $view = 'filament.resources.email-history-resource.pages.view-email-history';

    public EmailHistory $emailHistory;

    public function mount(): void
    {
        $recordId = request('record');
        $this->emailHistory = EmailHistory::findOrFail($recordId);
    }
}
