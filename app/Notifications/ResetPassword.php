<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class ResetPassword extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(60),
            ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]
        );

        return (new MailMessage)
            ->view('emails.reset_password', ['url' => $url]);
    }
}
