<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends VerifyEmailNotification
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Email Akun Anda')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Terima kasih telah mendaftar di Sistem Reservasi Futsal.')
            ->line('Silakan klik tombol di bawah untuk memverifikasi email Anda.')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Link verifikasi akan berlaku selama 60 menit.')
            ->line('Jika Anda tidak melakukan registrasi, abaikan email ini.');
    }
}
