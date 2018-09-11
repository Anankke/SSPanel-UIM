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
        $accessToken = $params['access_token'];
        return $accessToken;
    }

    public static function getMuKeyFromReq($request)
    {
        $params = $request->getQueryParams();
        if (!isset($params['key'])) {
            return null;
        }
        $accessToken = $params['key'];
        return $accessToken;
    }
}
