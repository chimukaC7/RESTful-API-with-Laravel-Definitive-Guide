<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
//this is class fired after the created event fires in appServiceProvider
class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;//we do not need to pass explicitly the value of user to the view,
        // laravel is going to make it available to the view
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->text('emails.welcome');//to use a plain text view
        //return $this->view('emails.welcome');//to use a html view
        //using a markdown we will automatically have the html version as well as the plain text version
        return $this->markdown('emails.welcome')->subject('Please confirm your account');
    }
}
