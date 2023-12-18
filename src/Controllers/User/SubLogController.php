<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\SubscribeLog;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class SubLogController extends BaseController
{
    /**
     * 订阅记录
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $logs = (new SubscribeLog())->orderBy('id', 'desc')->where('user_id', $this->user->id)->get();

        foreach ($logs as $log) {
            $log->request_time = Tools::toDateTime($log->request_time);
        }

        return $response->write($this->view()
            ->assign('subscribe_log_retention_days', Config::obtain('subscribe_log_retention_days'))
            ->assign('logs', $logs)
            ->fetch('user/subscribe_log.tpl'));
    }
}
