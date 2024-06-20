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
     * GET /mod_mu/func/ping
     */
    public function ping(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::success($response, 'Pong? Pong!');
    }

    /**
     * GET /mod_mu/func/detect_rules
     */
    public function getDetectRules(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $rules = DetectRule::all()->toArray();

        return ResponseHelper::successWithDataEtag($request, $response, $rules);
    }
}
