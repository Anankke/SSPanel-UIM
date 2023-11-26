<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class DetectRuleController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $rules = (new DetectRule())->get();

        return $response->write($this->view()
            ->assign('rules', $rules)
            ->fetch('user/detect/index.tpl'));
    }
}
