<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReadingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'La lecture vous manque ? 📚 — AGAU Éditions',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reading_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
