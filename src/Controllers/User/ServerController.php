<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class ServerController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $nodes = $this->user->getUserFrontEndNodes();
        $node_list = [];

        foreach ($nodes as $node) {
            $node_list[] = [
                'id' => $node->id,
                'name' => $node->name,
                'class' => (int) $node->node_class,
                'color' => $node->color,
                'sort' => $node->sort(),
                'online_user' => $node->online_user,
                'online' => $node->getNodeOnlineStatus(),
                'traffic_rate' => $node->traffic_rate,
                'is_dynamic_rate' => $node->is_dynamic_rate,
                'node_bandwidth' => Tools::autoBytes($node->node_bandwidth),
                'node_bandwidth_limit' => $node->node_bandwidth_limit === 0 ? '无限制' :
                    Tools::autoBytes($node->node_bandwidth_limit),
            ];
        }

        return $response->write(
            $this->view()
                ->assign('servers', $node_list)
                ->fetch('user/server.tpl')
        );
    }
}
