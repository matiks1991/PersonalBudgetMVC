<?php

namespace App;

use App\Config;
use Mailgun\Mailgun;

//Mail

class Mail
{
    //Send a message
    public static function send($to, $subject, $text, $html)
    {
        // First, instantiate the SDK with your API credentials
        $mg = Mailgun::create(Config::MAILGUN_API_KEY, 'https://api.eu.mailgun.net'); // For EU servers
        $domain = Config::MAILGUN_DOMAIN;

        // Now, compose and send your message.
        $mg->messages()->send($domain, [
        'from'    => 'Moj_Budzet@mail.com',
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
        'html'    => $html
        ]);
    }
}