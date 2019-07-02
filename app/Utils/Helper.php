<?php


namespace App\Utils;


class Helper
{
    public static function getParam($request, $queryString): ?string
    {
        return $request->getQueryParams()[$queryString] ?? null;
    }

    public static function getData($request, $name)
    {
        return $request->getParsedBody()[$name];
    }
}
