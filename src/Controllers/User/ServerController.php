<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

/**
 *  User ServerController
 */
final class ServerController extends BaseController
{
    /**
     * @throws Exception
     */
    public function server(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $query = Node::query();
        $query->where('type', 1);

        if (! $user->is_admin) {
            $group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
            $query->whereIn('node_group', $group);
        }

        $nodes = $query->orderBy('node_class')->orderBy('name')->get();
        $all_node = [];

        foreach ($nodes as $node) {
            $array_node = [];
            $array_node['id'] = $node->id;
            $array_node['name'] = $node->name;
            $array_node['class'] = (int) $node->node_class;
            $array_node['sort'] = (int) $node->sort;
            $array_node['info'] = $node->info;
            $array_node['online_user'] = $node->online_user;
            $array_node['online'] = $node->getNodeOnlineStatus();
            $array_node['traffic_rate'] = $node->traffic_rate;
            $array_node['status'] = $node->status;
            $array_node['traffic_used'] = (int) Tools::flowToGB($node->node_bandwidth);
            $array_node['traffic_limit'] = (int) Tools::flowToGB($node->node_bandwidth_limit);
            $array_node['bandwidth'] = $node->getNodeSpeedlimit();

            $all_node[] = $array_node;
        }

        return $response->write(
            $this->view()
                ->assign('servers', $all_node)
                ->fetch('user/server.tpl')
        );
    }
}
