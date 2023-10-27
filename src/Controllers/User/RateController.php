<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Services\DynamicRate;
use App\Utils\ResponseHelper;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function array_fill;
use function json_encode;

final class RateController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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
            if ($node->node_bandwidth_limit !== 0 && $node->node_bandwidth_limit <= $node->node_bandwidth) {
                continue;
            }

            $array_node = [];
            $array_node['id'] = $node->id;
            $array_node['name'] = $node->name;

            $all_node[] = $array_node;
        }

        return $response->write(
            $this->view()
                ->assign('nodes', $all_node)
                ->fetch('user/rate.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $node = Node::find($antiXss->xss_clean($request->getParam('node_id')));

        if ($node === null) {
            return ResponseHelper::error($response, '节点不存在');
        }

        if ($node->is_dynamic_rate) {
            $dynamic_rate_config = json_decode($node->dynamic_rate_config);
            $rates = DynamicRate::getFullDayRates(
                (float) $dynamic_rate_config?->max_rate,
                (int) $dynamic_rate_config?->max_rate_time,
                (float) $dynamic_rate_config?->min_rate,
                (int) $dynamic_rate_config?->min_rate_time,
            );
        } else {
            $rates = array_fill(0, 24, $node->traffic_rate);
        }

        $event = json_encode([
            'drawChart' => [
                'msg' => $node->name,
                'data' => $rates,
            ],
        ]);

        return $response->withHeader('HX-Trigger', $event)->withJson([
            'ret' => 1,
        ]);
    }
}
