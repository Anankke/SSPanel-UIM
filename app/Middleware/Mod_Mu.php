<?php

namespace App\Middleware;

use App\Services\Config;
use App\Models\Node;

class Mod_Mu
{
    /**
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param callable              $next
     *
     * @return \Slim\Http\Response
     */
    public function __invoke($request, $response, $next)
    {
        $key = $request->getQueryParam('key');
        if ($key === null) {
            $res['ret'] = 0;
            $res['data'] = 'null';
            return $response->write(json_encode($res));
        }

        $auth = false;
        $keys = Config::getMuKey();
        foreach ($keys as $k) {
            if ($key == $k) {
                $auth = true;
                break;
            }
        }
        $node = Node::where('node_ip', 'LIKE', $_SERVER['REMOTE_ADDR'] . '%')->first();
        if ($node == null && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            $res['ret'] = 0;
            $res['data'] = 'token or source is invalid, Your ip address is ' . $_SERVER['REMOTE_ADDR'];
            return $response->write(json_encode($res));
        }

        if ($auth == false) {
            $res['ret'] = 0;
            $res['data'] = 'token or source is invalid';
            return $response->write(json_encode($res));
        }

        return $next($request, $response);
    }
}
