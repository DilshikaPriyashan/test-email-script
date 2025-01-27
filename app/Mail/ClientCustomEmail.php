<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientCustomEmail extends Mailable
{
    use SerializesModels;

    public function __construct(
        protected readonly string $content,
        protected readonly string $subjects,
        protected readonly Address $fromAddress
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjects,
            from: $this->fromAddress
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.client-custom-email',
            with: [
                'content' => $this->content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
