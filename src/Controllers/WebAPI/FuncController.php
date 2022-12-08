<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class FuncController extends BaseController
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
    public function getDetectLogs(Request $request, Response $response, array $args): ResponseInterface
    {
        $rules = DetectRule::all();

        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => $rules,
        ]);
    }

    // Dummy function
    /**
     * @param array     $args
     */
    public function getBlockip(Request $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => [],
        ]);
    }

    /**
     * @param array     $args
     */
    public function getUnblockip(Request $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => [],
        ]);
    }

    /**
     * @param array     $args
     */
    public function addBlockIp(Request $request, Response $response, array $args)
    {
        $res = [
            'ret' => 1,
            'data' => 'ok',
        ];
        return $response->withJson($res);
    }
}
