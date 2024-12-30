<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendMailForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Forgot Password')
                    ->view('send_mail_forgot_password', ['data' =>  $this->data]);
    }
}
