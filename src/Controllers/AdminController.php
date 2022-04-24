<?php
namespace App\Controllers;

use App\Services\Analytics;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *  Admin Controller
 */
class AdminController extends UserController
{
    /**
     * 后台首页
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args)
    {
        $sts = new Analytics();
        $data = [
            'total' => [
                'user' => $sts->getTotalUser(),
                'node' => $sts->getTotalNodes(),
                'traffic' => $sts->getTotalTraffic(),
            ],
            'check-in' => [
                'none' => $sts->getTotalUser() - $sts->getCheckinUser(),
                'last' => $sts->getCheckinUser() - $sts->getTodayCheckinUser(),
                'today' => $sts->getTodayCheckinUser(),
            ],
            'node' => [
                'online' => $sts->getAliveNodes(),
                'offline' => $sts->getTotalNodes() - $sts->getAliveNodes(),
            ],
            'user' => [
                'none' => $sts->getUnusedUser(),
                'oneDayAgo' => $sts->getTotalUser() - $sts->getOnlineUser(86400) - $sts->getUnusedUser(),
                'inOneDay' => $sts->getOnlineUser(86400) - $sts->getOnlineUser(3600),
                'inOneHour' => $sts->getOnlineUser(3600) - $sts->getOnlineUser(60),
                'inOneMin' => $sts->getOnlineUser(60),
            ],
            'traffic' => [
                'today' => round($sts->getRawTodayTrafficUsage() / 1073741824, 2),
                'last' => round($sts->getRawLastTrafficUsage() / 1073741824, 2),
                'over' => round($sts->getRawUnusedTrafficUsage() / 1073741824, 2),
            ],
        ];

        return $response->write(
            $this->view()
                ->assign('data', $data)
                ->display('admin/index.tpl')
        );
    }

    /**
     * 统计信息
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function sys($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/index.tpl')
        );
    }
}
