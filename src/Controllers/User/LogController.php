<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\UserSubscribeLog;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class LogController extends BaseController
{
    /**
     * 订阅记录
     *
     * @throws Exception
     */
    public function subscribe(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $logs = UserSubscribeLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->get();

        return $response->write($this->view()
            ->assign('logs', $logs)
            ->registerClass('Tools', Tools::class)
            ->fetch('user/subscribe/log.tpl'));
    }

    /**
     * @throws Exception
     */
    public function detect(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $logs = DetectLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->get();

        return $response->write($this->view()
            ->assign('logs', $logs)
            ->registerClass('Tools', Tools::class)
            ->fetch('user/detect/log.tpl'));
    }
}
