<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\LoginIp;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\SubscribeLog;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Smarty\Exception;
use function str_replace;
use function time;

final class ProfileController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $logins = [];
        $subs = [];
        $ips = (new OnlineLog())->where('user_id', $this->user->id)
            ->where('last_time', '>', time() - 90)->orderByDesc('last_time')->get();

        if (Config::obtain('login_log')) {
            $logins = (new LoginIp())->where('userid', $this->user->id)
                ->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();
        }

        if (Config::obtain('subscribe_log')) {
            $subs = (new SubscribeLog())->where('user_id', $this->user->id)->orderBy('id', 'desc')->take(10)->get();
        }

        foreach ($ips as $ip) {
            $ip->ip = str_replace('::ffff:', '', $ip->ip);
            $ip->location = Tools::getIpLocation($ip->ip);
            $ip->node_name = (new Node())->where('id', $ip->node_id)->first()->name;
            $ip->last_time = Tools::toDateTime((int) $ip->last_time);
        }

        foreach ($logins as $login) {
            $login->datetime = Tools::toDateTime((int) $login->datetime);
            $login->location = Tools::getIpLocation($login->ip);
        }

        foreach ($subs as $sub) {
            $sub->request_time = Tools::toDateTime($sub->request_time);
        }

        return $response->write(
            $this->view()
                ->assign('ips', $ips)
                ->assign('logins', $logins)
                ->assign('subs', $subs)
                ->fetch('user/profile.tpl')
        );
    }
}
