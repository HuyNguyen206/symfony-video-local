<?php

namespace App\MessageHandler;

use App\Message\SmsNotiMessage;

class SmsHandler
{
    public function __invoke(SmsNotiMessage $message)
    {
        dd($message);
        // ... do some work - like sending an SMS message!
    }
}