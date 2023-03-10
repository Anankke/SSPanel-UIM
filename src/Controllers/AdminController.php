<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Analytics;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

/*
 *  Admin Controller
 */
final class AdminController extends BaseController
{
    /**
     * 后台首页
     */
    public function index(ServerRequest $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('sts', new Analytics())
                ->fetch('admin/index.tpl')
        );
    }
}
