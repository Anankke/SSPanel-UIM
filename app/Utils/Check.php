<?php


namespace App\Utils;

class Check
{
    //
    public static function isEmailLegal($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 32) {
            return true;
        } else {
            return false;
        }
    }
}
