<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerStatusChangedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public bool $isDisabled;
    public string $storeName;
    public string $storePhone;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, bool $isDisabled, string $storeName, string $storePhone)
    {
        $this->user = $user;
        $this->isDisabled = $isDisabled;
        $this->storeName = $storeName;
        $this->storePhone = $storePhone;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isDisabled
            ? 'Account Disabled - ' . $this->storeName
            : 'Account Reactivated - ' . $this->storeName;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-status-changed',
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
