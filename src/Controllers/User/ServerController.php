<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Models\User;
use App\Utils\Tools;
use App\Utils\URL;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *  User ServerController
 */
final class ServerController extends BaseController
{
    /**
     * @param array     $args
     */
    public function userServerPage(Request $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $query = Node::query();
        $query->where('type', 1)->whereNotIn('sort', [9]);
        if (! $user->is_admin) {
            $group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
            $query->whereIn('node_group', $group);
        }
        $nodes = $query->orderBy('node_class')->orderBy('name')->get();
        $all_node = [];
        foreach ($nodes as $node) {
            /** @var Node $node */

            $array_node = [];
            $array_node['id'] = $node->id;
            $array_node['name'] = $node->name;
            $array_node['class'] = $node->node_class;
            $array_node['sort'] = $node->sort;
            $array_node['info'] = $node->info;
            $array_node['flag'] = $node->getNodeFlag();
            $array_node['online_user'] = $node->getNodeOnlineUserCount();
            $array_node['online'] = $node->getNodeOnlineStatus();
            $array_node['load'] = $node->getNodeLoad();
            $array_node['uptime'] = $node->getNodeUptime();
            $array_node['traffic_rate'] = $node->traffic_rate;
            $array_node['status'] = $node->status;
            $array_node['traffic_used'] = (int) Tools::flowToGB($node->node_bandwidth);
            $array_node['traffic_limit'] = (int) Tools::flowToGB($node->node_bandwidth_limit);
            $array_node['bandwidth'] = $node->getNodeSpeedlimit();

            $all_connect = [];
            if (\in_array($node->sort, [0])) {
                if ($node->mu_only !== 1) {
                    $all_connect[] = 0;
                }
                if ($node->mu_only !== -1) {
                    $mu_node_query = Node::query();
                    $mu_node_query->where('sort', 9)->where('type', '1');
                    if (! $user->is_admin) {
                        $mu_node_query->where('node_class', '<=', $user->class)->whereIn('node_group', $group);
                    }
                    $mu_nodes = $mu_node_query->get();
                    foreach ($mu_nodes as $mu_node) {
                        if (User::where('port', $mu_node->server)->where('is_multi_user', '<>', 0)->first() !== null) {
                            $all_connect[] = $node->getOffsetPort($mu_node->server);
                        }
                    }
                }
            } else {
                $all_connect[] = 0;
            }
            $array_node['connect'] = $all_connect;

            $all_node[$node->node_class + 1000][] = $array_node;
        }

        return $response->write(
            $this->view()
                ->assign('servers', $all_node)
                ->display('user/server.tpl')
        );
    }
}
