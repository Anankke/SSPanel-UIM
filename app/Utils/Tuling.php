<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;

class Tuling
{
    public static function chat($user, $text)
    {
        if (Config::get('enable_tuling') == 'true') {
            $data = array();
            $data['key'] = Config::get('tuling_apikey');
            $data['userid'] = $user;
            $data['info'] = $text;

            $param = json_encode($data);


            $sock = new HTTPSocket;
            $sock->connect("www.tuling123.com", 80);
            $sock->set_method('POST');
            $sock->add_header('Content-Type', 'application/json');
            $sock->query('/openapi/api', $param);

            $result = $sock->fetch_body();
            $result_array = json_decode($result, true);
            return $result_array['text'];
        }
    }
}
