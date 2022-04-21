<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Node;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiController extends BaseController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeList($request, $response, $args): ResponseInterface
    {
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
    public function getNodeRelayList($request, $response, $args)
    {
        $text = '';
        $nodes = Node::where('server', 'like', '%relayserver%')
        ->where('type', '1')
        ->where('sort', '11')
        ->get();

        foreach ($nodes as $node)
        {
            $text .= $node->server . PHP_EOL;
        }

        return $text;
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeInfo($request, $response, $args): ResponseInterface
    {
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
    public function getNodeStatus($request, $response, $args): ResponseInterface
    {
        $node = Node::find($args['id']);
        if ($node == null) {
            return $response->withJson([
                "ret" => 0,
                "msg" => 'This node id was not found.',
            ]);
        }
        if ($node->isNodeOnline()) {
            return $response->withJson([
                "ret" => 1,
                "data" => 'online',
            ]);
        } else {
            return $response->withJson([
                "ret" => 1,
                "data" => 'offline',
            ]);
        }
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeId($request, $response, $args): ResponseInterface
    {
        $node = Node::where('node_ip', $args['ip'])->get();

        if ($node->count() == 0) {
            return $response->withJson([
                "ret" => 0,
                "msg" => 'This ip was not found.',
            ]);
        } else {
            return $response->withJson([
                "ret" => 1,
                "data" => $node,
                "total" => $node->count(),
            ]);
        }
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ping($request, $response, $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'pong',
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function change($request, $response, $args): ResponseInterface
    {
        $node = Node::find($args['id']);
        if (empty($node)) {
            $node = Node::where('server', 'like', '%'.$args['id'].'%')->first();
            if (empty($node)) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => 'This node was not found.',
                ]);
            }
        }
        if ($node->sort != '11') {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Only nodes of type v2ray are supported.',
            ]);
        }

        $current_node_port = explode(';', $node->server);
        $current_node_port = $current_node_port[1];
        $new_node_port = $current_node_port + 2;
        $node->server = str_replace(";$current_node_port;", ";$new_node_port;", $node->server);
        $node->save();

        return $response->withJson([
            'ret' => 1,
            'data' => $new_node_port,
        ]);
    }
}
