<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Config;
use App\Models\Link;
use App\Models\SubscribeLog;
use App\Services\RateLimit;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function in_array;
use function strtotime;

final class SubController extends BaseController
{
    /**
     * @throws ClientExceptionInterface
     * @throws GuzzleException
     * @throws RedisException
     * @throws TelegramSDKException
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $err_msg = '订阅链接无效';
        $subtype = $args['subtype'];
        $subtype_list = ['json', 'clash', 'sip008', 'singbox', 'v2rayjson', 'sip002', 'ss', 'v2ray', 'trojan'];

        if (! $_ENV['Subscribe'] ||
            ! in_array($subtype, $subtype_list) ||
            'https://' . $request->getHeaderLine('Host') !== $_ENV['subUrl']
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $token = $this->antiXss->xss_clean($args['token']);

        if ($_ENV['enable_rate_limit'] &&
            (! (new RateLimit())->checkRateLimit('sub_ip', $request->getServerParam('REMOTE_ADDR')) ||
            ! (new RateLimit())->checkRateLimit('sub_token', $token))
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $link = (new Link())->where('token', $token)->first();

        if ($link === null || ! $link->isValid()) {
            return ResponseHelper::error($response, $err_msg);
        }

        $user = $link->user();
        $sub_info = Subscribe::getContent($user, $subtype);

        $content_type = match ($subtype) {
            'clash' => 'application/yaml',
            'json','sip008','singbox','v2rayjson' => 'application/json',
            default => 'text/plain',
        };

        $sub_details = ' upload=' . $user->u
        . '; download=' . $user->d
        . '; total=' . $user->transfer_enable
        . '; expire=' . strtotime($user->class_expire);
        // Clash specific
        $sub_content_disposition = 'attachment; filename=' . $_ENV['appName'];
        $sub_profile_update_interval = 6;
        $sub_profile_web_page_url = $_ENV['baseUrl'];

        if (Config::obtain('subscribe_log')) {
            (new SubscribeLog())->add(
                $user,
                $subtype,
                $this->antiXss->xss_clean($request->getHeaderLine('User-Agent'))
            );
        }

        if ($subtype === 'clash') {
            return $response->withHeader('Subscription-Userinfo', $sub_details)
                ->withHeader('Content-Disposition', $sub_content_disposition)
                ->withHeader('Profile-Update-Interval', $sub_profile_update_interval)
                ->withHeader('Profile-Web-Page-Url', $sub_profile_web_page_url)
                ->withHeader('Content-Type', $content_type)
                ->write($sub_info);
        }

        return $response->withHeader('Subscription-Userinfo', $sub_details)
            ->withHeader('Content-Type', $content_type)
            ->write($sub_info);
    }
}
