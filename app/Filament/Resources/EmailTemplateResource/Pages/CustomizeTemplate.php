<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CustomizeTemplate extends Page
{
    protected static string $resource = EmailTemplateResource::class;

    protected static string $view = 'filament.resources.email-template-resource.pages.customize-template';

    public EmailTemplate $emailTemplate;

    public string $htmlBody;

    public array $customAttributes;

    public function mount(): void
    {
        $recordId = request('record');
        $this->emailTemplate = EmailTemplate::findOrFail($recordId);
        $this->htmlBody = $this->emailTemplate->content;
        $customAttributes = $this->emailTemplate->attributes;
        $this->customAttributes = Arr::pluck($customAttributes, 'key');
    }

    #[On('save-template')]
    public function saveTemplate(string $html)
    {
        $this->emailTemplate->content = Str::of($html)->replace('<body', '<div')->replace('</body>', '</div>');
        $this->emailTemplate->save();
    }
}
