<?php


namespace App\Services\Mail;

abstract class Base
{
    abstract public function send($to, $subject, $text, $file);
}
