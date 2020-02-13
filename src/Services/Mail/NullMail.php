<?php

namespace App\Services\Mail;

class NullMail extends Base
{

    public function __construct()
    {
    }

    public function getConfig()
    {
        return [
        ];
    }

    public function send($to_address, $subject_raw, $text, $files)
    {
        echo '';
    }
}
