<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\Analytics;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_encode;

final class TrafficLogController extends BaseController
{
    /**
     * 订阅记录
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (! Config::obtain('traffic_log')) {
            return $response->withRedirect('/user');
        }

        $logs = [];
        $hourly_usage = Analytics::getUserTodayHourlyUsage($this->user->id);

        foreach ($hourly_usage as $hour => $usage) {
            $logs[] = Tools::flowToMB((int) $usage);
        }

        return $response->write($this->view()
            ->assign('logs', json_encode($logs))
            ->fetch('user/traffic_log.tpl'));
    }
}
