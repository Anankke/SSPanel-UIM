<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Utils\ResponseHelper;
use App\Models\StreamMedia;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;
use const JSON_UNESCAPED_SLASHES;
use const VERSION;

final class NodeController extends BaseController
{
    /**
     * @param array     $args
     */
    public function saveReport(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $request->getParam('node_id');
        $content = $request->getParam('content');
        $result = \json_decode(base64_decode($content), true);
        $report = new StreamMedia();
        $report->node_id = $node_id;
        $report->result = \json_encode($result);
        $report->created_at = \time();
        $report->save();

        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }
    public function getInfo(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $args['id'];
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
        }

        $data = [
            'node_speedlimit' => $node->node_speedlimit,
            'sort' => $node->sort,
            'server' => $node->server,
            'custom_config' => json_decode($node->custom_config, true, JSON_UNESCAPED_SLASHES),
            'type' => 'SSPanel-UIM',
            'version' => VERSION,
        ];

        return ResponseHelper::successWithDataEtag($request, $response, $data);
    }
}
