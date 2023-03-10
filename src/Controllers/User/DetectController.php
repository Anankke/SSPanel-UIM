<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class DetectController extends BaseController
{
    public function index(ServerRequest $request, Response $response, array $args)
    {
        $rules = DetectRule::get();

        return $response->write($this->view()
            ->assign('rules', $rules)
            ->fetch('user/detect/index.tpl'));
    }
}
