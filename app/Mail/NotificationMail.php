<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingsHelper;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationMessage;
    public $actionRoute;
    public $actionParams;
    public $appName;
    public $appLogo;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $actionRoute = null, $actionParams = null)
    {
        $this->notificationMessage = $message;
        $this->actionRoute = $actionRoute;
        $this->actionParams = $actionParams;
        $this->appName = SettingsHelper::appName();
        $this->appLogo = SettingsHelper::get('app_logo', '/images/logo.png');
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'New Notification',
            from: new Address(
                SettingsHelper::mailFromAddress(),
                SettingsHelper::mailFromName()
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
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