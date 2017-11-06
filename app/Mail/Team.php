<?php

namespace Zoomov\Mail;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Team extends Mailable
{
    use Queueable, SerializesModels;

    public $link, $username, $title, $occupations;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link, $username, $title, $occupations)
    {
        $this->link;
        $this->username;
        $this->title;
        $this->occupations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('messages.team', ['name' => Auth::user()->username, 'title'=>$this->title]))
            ->markdown('emails.team');
    }
}
