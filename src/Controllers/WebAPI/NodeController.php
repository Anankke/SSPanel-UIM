<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;
use const VERSION;

final class NodeController extends BaseController
{
    public function getInfo(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $args['id'];
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Node not found.',
            ]);
        }

        $data = [
            'node_group' => $node->node_group,
            'node_class' => $node->node_class,
            'node_speedlimit' => $node->node_speedlimit,
            'traffic_rate' => $node->traffic_rate,
            'sort' => $node->sort,
            'server' => $node->server,
            'custom_config' => json_decode($node->custom_config, true, JSON_UNESCAPED_SLASHES),
            'type' => 'SSPanel-UIM',
            'version' => VERSION,
        ];

        return ResponseHelper::successWithDataEtag($request, $response, [
            'ret' => 1,
            'data' => $data,
        ]);
    }
}
