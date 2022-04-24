<?php
namespace App\Controllers\Mod_Mu;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use App\Models\Node;
use App\Models\Log;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class FuncController extends BaseController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ping($request, $response, $args)
    {
        $res = [
            'ret' => 1,
            'data' => 'pong',
        ];
        return $response->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getDetectLogs($request, $response, $args): ResponseInterface
    {
        $rules = DetectRule::all();

        $res = [
            'ret' => 1,
            'data' => $rules,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($rules);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getBlockip($request, $response, $args): ResponseInterface
    {
        $block_ips = [];

        $res = [
            'ret' => 1,
            'data' => $block_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($block_ips);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getUnblockip($request, $response, $args): ResponseInterface
    {
        $unblock_ips = [];

        $res = [
            'ret' => 1,
            'data' => $unblock_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($unblock_ips);
        if ($header_etag == $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function addBlockIp($request, $response, $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id == '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node == null) {
            $res = [
                'ret' => 0,
            ];
            return $response->withJson($res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $ip = $log['ip'];
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function log($request, $response, $args)
    {
        $type = $request->getParam('type');
        $reporter = $request->getParam('reporter');
        $level = $request->getParam('level');
        $msg = $request->getParam('msg');

        $l = new Log;
        $l->type = $type;
        $l->reporter = $reporter;
        $l->level = $level;
        $l->msg = $msg;
        $l->status = 0;
        $l->created_at = time();

        if ($l->save()) {
            return $response->withJson([
                'ret' => 1,
                'data' => 'ok',
            ]);
        }
    }
}
