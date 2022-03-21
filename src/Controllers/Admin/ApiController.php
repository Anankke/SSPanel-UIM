<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface;
use App\Models\Node;
use Slim\Http\{
    Request,
    Response
};

class ApiController extends BaseController {
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeList($request, $response, $args): ResponseInterface {
        return $response->withJson([
            "ret" => 1,
            "nodes" => Node::all(),
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeInfo($request, $response, $args): ResponseInterface {
        $node = Node::find($args['id']);

        return $response->withJson([
            "ret" => 1,
            "node" => $node,
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ping($request, $response, $args): ResponseInterface {
        return $response->withJson([
            'ret' => 1,
            'data' => 'pong'
        ]);
    }
}
