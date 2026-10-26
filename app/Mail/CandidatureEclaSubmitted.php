<?php

namespace App\Mail;

use App\Models\CandidatureEcla;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureEclaSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CandidatureEcla $candidature) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre candidature - Concours eCLA 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidatures.submitted',
        );
    }
}
