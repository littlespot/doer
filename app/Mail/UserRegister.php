<?php

namespace Zoomov\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Zoomov\User;

class UserRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, User $user)
    {
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('auth.welcome', ['name' => $this->user->username]))
            ->markdown('emails.register');
    }
}
