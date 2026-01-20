<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $link;
    protected $expiresIn;
    protected $expirationMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $link, $expiresIn, $expirationMinutes)
    {
        $this->user = $user;
        $this->link = $link;
        $this->expiresIn = $expiresIn;
        $this->expirationMinutes = $expirationMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'CampusMaster - Réinitialisation de mot de passe',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.password-reset',
            with: [
                'user' => $this->user,
                'link' => $this->link,
                'expiresIn' => $this->expiresIn,
                'expirationMinutes' => $this->expirationMinutes,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
