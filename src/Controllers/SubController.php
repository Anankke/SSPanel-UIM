<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Link;
use App\Models\Setting;
use App\Models\SubscribeLog;
use App\Services\RateLimit;
use App\Services\Subscribe;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use voku\helper\AntiXSS;
use function in_array;
use function strtotime;

final class SubController extends BaseController
{
    /**
     * @throws RedisException
     */
    public static function getUniversalSubContent($request, $response, $args): ResponseInterface
    {
        $err_msg = '订阅链接无效';

        $subtype = $args['subtype'];
        $subtype_list = ['json', 'clash', 'sip008'];

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
            default => 'application/json',
        };

        $sub_details = ' upload=' . $user->u
        . '; download=' . $user->d
        . '; total=' . $user->transfer_enable
        . '; expire=' . strtotime($user->class_expire);

        if ($_ENV['subscribeLog']) {
            SubscribeLog::add($user, $subtype, $request->getHeaderLine('User-Agent'));
        }

        return $response->withHeader('Subscription-Userinfo', $sub_details)
            ->withHeader('Content-Type', $content_type)
            ->write($sub_info);
    }

    /**
     * @throws RedisException
     */
    public static function getTraditionalSubContent($request, $response, $args): ResponseInterface
    {
        $err_msg = '订阅链接无效';

        if (! $_ENV['Subscribe'] ||
            ! Setting::obtain('enable_traditional_sub') ||
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
        $params = $request->getQueryParams();

        $sub_types = [
            'sip002',
            'ss',
            'v2ray',
            'trojan',
        ];

        $sub_type = '';
        $sub_info = '';

        foreach ($params as $key => $value) {
            if (in_array($key, $sub_types) && $value === '1') {
                $sub_type = $key;
                $sub_info = Subscribe::getContent($user, $sub_type);
                break;
            }
        }

        // 记录订阅日志
        if ($_ENV['subscribeLog']) {
            SubscribeLog::add($user, $sub_type, $request->getHeaderLine('User-Agent'));
        }

        $sub_details = ' upload=' . $user->u
            . '; download=' . $user->d
            . '; total=' . $user->transfer_enable
            . '; expire=' . strtotime($user->class_expire);

        return $response->withHeader('Subscription-Userinfo', $sub_details)->write(
            $sub_info
        );
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

    public static function getTraditionalSubLink($user): string
    {
        $userid = $user->id;
        $token = Link::where('userid', $userid)->first();

        return $_ENV['subUrl'] . '/link/' . $token->token;
    }
}
