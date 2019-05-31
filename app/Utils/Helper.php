<?php


namespace App\Utils;

class Helper
{
    public static function redirect($url)
    {
    }

    public static function getTokenFromReq($request)
    {
        $params = $request->getQueryParams();
        if (!isset($params['access_token'])) {
            return null;
        }
        return $params['access_token'];
    }

    public static function getMuKeyFromReq($request)
    {
        $params = $request->getQueryParams();
        if (!isset($params['key'])) {
            return null;
        }
        return $params['key'];
    }
}
