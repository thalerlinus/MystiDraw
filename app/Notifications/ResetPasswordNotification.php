<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends BaseResetPassword
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset password notification mail message for the given URL.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('🔑 Passwort zurücksetzen - MystiDraw')
            ->greeting('👋 Hallo!')
            ->line('Sie erhalten diese E-Mail, weil wir eine Passwort-Reset-Anfrage für Ihr Konto erhalten haben.')
            ->action('Passwort zurücksetzen', $url)
            ->line('Dieser Link zum Zurücksetzen des Passworts läuft in ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' Minuten ab.')
            ->line('Falls Sie kein Passwort-Reset angefordert haben, sind keine weiteren Maßnahmen erforderlich.')
            ->salutation('Beste Grüße,<br>Das MystiDraw Team');
    }
}
