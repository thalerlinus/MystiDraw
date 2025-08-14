<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return $this->buildMailMessage($verificationUrl);
    }

    /**
     * Get the verify email notification mail message for the given URL.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('📧 E-Mail-Adresse bestätigen - MystiDraw')
            ->greeting('👋 Willkommen bei MystiDraw!')
            ->line('Vielen Dank für Ihre Registrierung! Um Ihr Konto zu aktivieren, müssen Sie Ihre E-Mail-Adresse bestätigen.')
            ->action('E-Mail-Adresse bestätigen', $url)
            ->line('Falls Sie kein Konto erstellt haben, sind keine weiteren Maßnahmen erforderlich.')
            ->line('Nach der Bestätigung können Sie sofort mit dem Sammeln von Mystery-Box-Losen beginnen!')
            ->salutation('Beste Grüße,<br>Das MystiDraw Team');
    }
}
