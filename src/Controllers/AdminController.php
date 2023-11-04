<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Analytics;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class AdminController extends BaseController
{
    /**
     * 后台首页
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $today_income = Analytics::getIncome('today');
        $yesterday_income = Analytics::getIncome('yesterday');
        $this_month_income = Analytics::getIncome('this month');
        $total_income = Analytics::getIncome('total');
        $total_user = Analytics::getTotalUser();
        $checkin_user = Analytics::getCheckinUser();
        $today_checkin_user = Analytics::getTodayCheckinUser();
        $inactive_user = Analytics::getInactiveUser();
        $active_user = Analytics::getActiveUser();
        $total_node = Analytics::getTotalNode();
        $alive_node = Analytics::getAliveNode();
        $raw_today_traffic = Analytics::getRawGbTodayTrafficUsage();
        $raw_last_traffic = Analytics::getRawGbLastTrafficUsage();
        $raw_unused_traffic = Analytics::getRawGbUnusedTrafficUsage();
        $today_traffic = Analytics::getTodayTrafficUsage();
        $last_traffic = Analytics::getLastTrafficUsage();
        $unused_traffic = Analytics::getUnusedTrafficUsage();

        return $response->write(
            $this->view()
                ->assign('today_income', $today_income)
                ->assign('yesterday_income', $yesterday_income)
                ->assign('this_month_income', $this_month_income)
                ->assign('total_income', $total_income)
                ->assign('total_user', $total_user)
                ->assign('checkin_user', $checkin_user)
                ->assign('today_checkin_user', $today_checkin_user)
                ->assign('inactive_user', $inactive_user)
                ->assign('active_user', $active_user)
                ->assign('total_node', $total_node)
                ->assign('alive_node', $alive_node)
                ->assign('raw_today_traffic', $raw_today_traffic)
                ->assign('raw_last_traffic', $raw_last_traffic)
                ->assign('raw_unused_traffic', $raw_unused_traffic)
                ->assign('today_traffic', $today_traffic)
                ->assign('last_traffic', $last_traffic)
                ->assign('unused_traffic', $unused_traffic)
                ->fetch('admin/index.tpl')
        );
    }
}
