<?php

namespace Zoomov\Mail;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Team extends Mailable
{
    use Queueable, SerializesModels;

    public $link, $username, $title, $url, $occupations;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link, $username, $project, $occupations)
    {
        $this->link = $link;
        $this->url = config('app.url').'/projects/'.$project->id;
        $this->username = $username;
        $this->title = $project->title;
        $this->occupations = $occupations;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('messages.team', ['user' => Auth::user()->username, 'title'=>$this->title]))
            ->markdown('emails.team');
    }
}
