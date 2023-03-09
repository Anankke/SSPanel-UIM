<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Node;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class ApiController extends BaseController
{
    public function getNodeList(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'nodes' => Node::all(),
        ]);
    }

    public function getNodeInfo(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = Node::find($args['id']);

        return $response->withJson([
            'ret' => 1,
            'node' => $node,
        ]);
    }

    public function ping(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'pong',
        ]);
    }
}
