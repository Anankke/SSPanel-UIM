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
    public function ping(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->withJson([
            'ret' => 1,
            'data' => 'Pong? Pong!',
        ]);
    }

    public function getDetectLogs(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $rules = DetectRule::all();

        return ResponseHelper::etagJson($request, $response, [
            'ret' => 1,
            'data' => $rules,
        ]);
    }
}
