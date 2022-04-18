<?php

declare(strict_types=1);

namespace App\Controllers\Mod_Mu;

use App\Controllers\BaseController;
use App\Utils\Tools;
use Node;
use Psr\Http\Message\ResponseInterface;
use Request;

class FuncController extends BaseController
{
    /**
     * @param array     $args
     */
    public function ping(Request $request, Response $response, array $args)
    {
        $res = [
            'ret' => 1,
            'data' => 'pong',
        ];
        return $response->withJson($res);
    }

    /**
     * @param array     $args
     */
    public function get_detect_logs(Request $request, Response $response, array $args): ResponseInterface
    {
        $rules = DetectRule::all();

        $res = [
            'ret' => 1,
            'data' => $rules,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($rules);
        if ($header_etag === $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param array     $args
     */
    public function get_blockip(Request $request, Response $response, array $args): ResponseInterface
    {
        $block_ips = BlockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            'ret' => 1,
            'data' => $block_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($block_ips);
        if ($header_etag === $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param array     $args
     */
    public function get_unblockip(Request $request, Response $response, array $args): ResponseInterface
    {
        $unblock_ips = UnblockIp::Where('datetime', '>', time() - 60)->get();

        $res = [
            'ret' => 1,
            'data' => $unblock_ips,
        ];
        $header_etag = $request->getHeaderLine('IF_NONE_MATCH');
        $etag = Tools::etag($unblock_ips);
        if ($header_etag === $etag) {
            return $response->withStatus(304);
        }
        return $response->withHeader('ETAG', $etag)->withJson($res);
    }

    /**
     * @param array     $args
     */
    public function addBlockIp(Request $request, Response $response, array $args)
    {
        $params = $request->getQueryParams();

        $data = $request->getParam('data');
        $node_id = $params['node_id'];
        if ($node_id === '0') {
            $node = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();
            $node_id = $node->id;
        }
        $node = Node::find($node_id);
        if ($node === null) {
            $res = [
                'ret' => 0,
            ];
            return $response->withJson($res);
        }

        if (count($data) > 0) {
            foreach ($data as $log) {
                $ip = $log['ip'];

                $exist_ip = BlockIp::where('ip', $ip)->first();
                if ($exist_ip !== null) {
                    continue;
                }

                // log
                $ip_block = new BlockIp();
                $ip_block->ip = $ip;
                $ip_block->nodeid = $node_id;
                $ip_block->datetime = time();
                $ip_block->save();
            }
        }

        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }
}
