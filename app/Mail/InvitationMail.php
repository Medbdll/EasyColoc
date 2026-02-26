<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $personalMessage;
    public $colocation;

    public function __construct(Invitation $invitation, $personalMessage = null, $colocation = null)
    {
        $this->invitation = $invitation;
        $this->personalMessage = $personalMessage;
        $this->colocation = $colocation ?? $invitation->colocation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join {$this->colocation->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [
                'invitation' => $this->invitation,
                'personalMessage' => $this->personalMessage,
                'colocation' => $this->colocation,
                'acceptUrl' => route('invitations.accept', $this->invitation->token),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
