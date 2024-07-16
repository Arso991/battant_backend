<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase
{

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('Mail de vérification !')
            ->line('Cliquez sur le lien ci-après pour vérifier votre mail et accéder à votre compte.')
            ->action('Lien de vérification', $verificationUrl)
            ->line('Si vous n\'avez pas créé de compte, aucune autre action n\'est requise. Merci!');
    }
}
