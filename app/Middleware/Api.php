<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Factory;
use App\Utils\Helper;

class Api
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $accessToken = Helper::getTokenFromReq($request);
        if ($accessToken == null) {
            $res['ret'] = 0;
            $res['msg'] = "token is null";
            $response->getBody()->write(json_encode($res));
            return $response;
        }
        $storage = Factory::createTokenStorage();
        $token = $storage->get($accessToken);
        if ($token == null) {
            $res['ret'] = 0;
            $res['msg'] = "token is null";
            $response->getBody()->write(json_encode($res));
            return $response;
        }
        if ($token->expireTime < time()) {
            $res['ret'] = 0;
            $res['msg'] = "token is expire";
            $response->getBody()->write(json_encode($res));
            return $response;
        }
        $response = $next($request, $response);
        return $response;
    }
}
