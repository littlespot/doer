<?php

namespace Zoomov\Helpers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line(trans('email.reset_link'))
            ->action(trans('email.reset_action'), url(config('app.url').route('password.reset', $this->token, false)))
            ->line(trans('email.reset_line'));
    }
}
