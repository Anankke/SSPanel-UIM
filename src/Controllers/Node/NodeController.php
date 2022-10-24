<?php

declare(strict_types=1);

namespace App\Controllers\Node;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Models\StreamMedia;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class NodeController extends BaseController
{
    /**
     * @param array     $args
     */
    public function saveReport(Request $request, Response $response, array $args): void
    {
        $node_id = $request->getParam('node_id');
        $content = $request->getParam('content');
        $result = \json_decode(base64_decode($content), true);
        $report = new StreamMedia();
        $report->node_id = $node_id;
        $report->result = \json_encode($result);
        $report->created_at = \time();
        $report->save();
        die('ok');
    }

    /**
     * @param array     $args
     */
    public function info(Request $request, Response $response, array $args)
    {
        $node_id = $args['id'];
        $load = $request->getParam('load');
        $uptime = $request->getParam('uptime');
        $node = Node::find($node_id);
        $node->load = $load;
        $node->uptime = $uptime;
        if (! $node->save()) {
            $res = [
                'ret' => 0,
                'data' => 'update failed',
            ];
            return $response->withJson($res);
        }
        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    /**
     * @param array     $args
     */
    public function getInfo(Request $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $args['id'];
        $node = Node::find($node_id);
        if ($node === null) {
            $res = [
                'ret' => 0,
            ];
            return $response->withJson($res);
        }
        if (\in_array($node->sort, [0])) {
            $node_explode = explode(';', $node->server);
            $node_server = $node_explode[0];
        } else {
            $node_server = $node->server;
        }
        $data = [
            'node_group' => $node->node_group,
            'node_class' => $node->node_class,
            'node_speedlimit' => $node->node_speedlimit,
            'traffic_rate' => $node->traffic_rate,
            'mu_only' => $node->mu_only,
            'sort' => $node->sort,
            'server' => $node_server,
            'custom_config' => \json_decode($node->custom_config, true, JSON_UNESCAPED_SLASHES),
            'type' => 'SSPanel-UIM',
            'version' => VERSION,
        ];

        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => $data,
        ]);
    }

    /**
     * @param array     $args
     */
    public function getAllInfo(Request $request, Response $response, array $args): ResponseInterface
    {
        $nodes = Node::where('node_ip', '<>', null)->where(
            static function ($query): void {
                $query->where('sort', '=', 0)
                    ->orWhere('sort', '=', 10)
                    ->orWhere('sort', '=', 12)
                    ->orWhere('sort', '=', 13)
                    ->orWhere('sort', '=', 14);
            }
        )->get();

        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => $nodes,
        ]);
    }
}
