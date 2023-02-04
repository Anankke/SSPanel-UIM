<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class FuncController extends BaseController
{
    /**
     * @param array     $args
     */
    public function ping(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'Pong? Pong!',
        ]);
    }

    /**
     * @param array     $args
     */
    public function getDetectLogs(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function getBlockip(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => [],
        ]);
    }

    /**
     * @param array     $args
     */
    public function getUnblockip(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => [],
        ]);
    }

    /**
     * @param array     $args
     */
    public function addBlockIp(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'ok',
        ]);
    }
}
