<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Config;
use App\Models\Link;
use App\Models\SubscribeLog;
use App\Services\RateLimit;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use voku\helper\AntiXSS;
use function in_array;
use function strtotime;

final class SubController extends BaseController
{
    /**
     * @param $request
     * @param $response
     * @param $args
     *
     * @return ResponseInterface
     *
     * @throws ClientExceptionInterface
     * @throws GuzzleException
     * @throws RedisException
     * @throws TelegramSDKException
     */
    public static function getUniversalSubContent($request, $response, $args): ResponseInterface
    {
        $err_msg = '订阅链接无效';

        $subtype = $args['subtype'];
        $subtype_list = ['json', 'clash', 'sip008', 'singbox', 'sip002', 'ss', 'v2ray', 'trojan'];

        if (! $_ENV['Subscribe'] ||
            ! in_array($subtype, $subtype_list) ||
            'https://' . $request->getHeaderLine('Host') !== $_ENV['subUrl']
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $antiXss = new AntiXSS();
        $token = $antiXss->xss_clean($args['token']);

        if ($_ENV['enable_rate_limit'] &&
            (! RateLimit::checkIPLimit($request->getServerParam('REMOTE_ADDR')) ||
            ! RateLimit::checkSubLimit($token))
        ) {
            return ResponseHelper::error($response, $err_msg);
        }

        $link = Link::where('token', $token)->first();

        if ($link === null || ! $link->isValid()) {
            return ResponseHelper::error($response, $err_msg);
        }

        $user = $link->user();
        $sub_info = Subscribe::getContent($user, $subtype);

        $content_type = match ($subtype) {
            'clash' => 'application/yaml',
            'json','sip008','singbox' => 'application/json',
            default => 'text/plain',
        };

        $sub_details = ' upload=' . $user->u
        . '; download=' . $user->d
        . '; total=' . $user->transfer_enable
        . '; expire=' . strtotime($user->class_expire);

        if (Config::obtain('subscribe_log')) {
            (new SubscribeLog())->add($user, $subtype, $antiXss->xss_clean($request->getHeaderLine('User-Agent')));
        }

        return $response->withHeader('Subscription-Userinfo', $sub_details)
            ->withHeader('Content-Type', $content_type)
            ->write($sub_info);
    }

    public static function getUniversalSubLink($user): string
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();

        if ($token === null) {
            $token = new Link();
            $token->userid = $userid;
            $token->token = Tools::genSubToken();
            $token->save();
        }

        return $_ENV['subUrl'] . '/sub/' . $token->token;
    }
}
