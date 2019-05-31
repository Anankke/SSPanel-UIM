<?php


namespace App\Utils;

class Check
{
    //
    public static function isEmailLegal($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 32;
    }
}
