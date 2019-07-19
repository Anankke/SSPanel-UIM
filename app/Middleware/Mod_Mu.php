<?php


namespace App\Middleware;

use App\Services\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Utils\Helper;
use App\Models\Node;
use App\Utils\DatatablesHelper;

class Mod_Mu
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $key = Helper::getParam($request, 'key');
        if ($key === null) {
            $res['ret'] = 0;
            $res['data'] = 'null';
            $response->getBody()->write(json_encode($res));
            return $response;
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
            // check iplc node 
            $iplcCheck = false;
            $db = new DatatablesHelper();
            $nodes = $db->query('SELECT * FROM `ss_node` WHERE `node_ip` not REGEXP \'^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$\' and `name` like \'%iplc%\'');
            foreach ($nodes as $node) {
                if ($_SERVER['REMOTE_ADDR'] == gethostbyname($node['node_ip'])) {
                    $iplcCheck = true;
                    break;
                } 
            }
            if (!$iplcCheck) {
                $res['ret'] = 0;
                $res['data'] = 'token or source is invalid, Your ip address is ' . $_SERVER['REMOTE_ADDR'];
                $response->getBody()->write(json_encode($res));
                return $response;
            }
        }

        if ($auth == false) {
            $res['ret'] = 0;
            $res['data'] = 'token or source is invalid';
            $response->getBody()->write(json_encode($res));
            return $response;
        }

        $response = $next($request, $response);
        return $response;
    }
}
