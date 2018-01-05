<?php

namespace App\Utils;

class Response
{
    public static function redirect($response, $to)
    {
        $newResponse = $response->withStatus(302)->withHeader('Location', $to);
        return $newResponse;
    }
}
