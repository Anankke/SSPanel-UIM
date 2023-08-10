<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Services\Cache;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use SmartyException;
use voku\helper\AntiXSS;
use function hash;
use function hash_hmac;
use function implode;
use function json_decode;
use function strcmp;
use function time;

/**
 *  OAuthController
 */
final class OAuthController extends BaseController
{
    /**
     * @throws SmartyException
     * @throws GuzzleException
     * @throws RedisException
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return match ($args['type']) {
            'slack' => $this->slack($request, $response, $args),
            'discord' => $this->discord($request, $response, $args),
            'telegram' => $this->telegram($request, $response, $args),
            default => $response->withStatus(404)->write($this->view()->fetch('404.tpl')),
        };
    }

    public function slack(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::error($response, '暂不支持');
    }

    /**
     * @throws GuzzleException
     * @throws RedisException
     */
    public function discord(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $redis = Cache::initRedis();

        if ($request->getParam('code') === null) {
            $state = Tools::genRandomChar(16);
            $redis->setex('discord_state:' . $user->id, 300, $state);
            $client_id = Setting::obtain('discord_client_id');
            $redirect_uri = $_ENV['baseUrl'] . '/oauth/discord';

            return $response->withJson([
                'ret' => 1,
                'redir' => 'https://discord.com/api/oauth2/authorize?client_id=' . $client_id .
                    '&redirect_uri=' . $redirect_uri .
                    '&response_type=code&scope=guilds.join identify&state=' . $state,
            ]);
        }

        $code = $request->getParam('code');
        $state = $request->getParam('state');

        if ($state !== $redis->get('discord_state:' . $user->id)) {
            return ResponseHelper::error($response, '参数错误');
        }

        $client = new Client();
        $discord_api_url = 'https://discord.com/api/oauth2/token';

        $code_headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $code_body = [
            'client_id' => Setting::obtain('discord_client_id'),
            'client_secret' => Setting::obtain('discord_client_secret'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $_ENV['baseUrl'] . '/oauth/discord',
        ];

        $code_response = $client->post($discord_api_url, [
            'headers' => $code_headers,
            'form_params' => $code_body,
        ]);

        if ($code_response->getStatusCode() !== 200) {
            return ResponseHelper::error($response, 'OAuth 请求失败');
        }

        $access_token = json_decode($code_response->getBody()->getContents())->access_token;
        $discord_user_url = 'https://discord.com/api/users/@me';

        $user_headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $access_token,
        ];

        $user_response = $client->get($discord_user_url, [
            'headers' => $user_headers,
        ]);

        if ($user_response->getStatusCode() !== 200) {
            return ResponseHelper::error($response, 'OAuth 请求失败');
        }

        $user->im_type = 2;
        $user->im_value = json_decode($user_response->getBody()->getContents())->id;
        $user->save();

        if (Setting::obtain('discord_guild_id') !== 0) {
            $discord_guild_url = 'https://discord.com/api/guilds/' . Setting::obtain('discord_guild_id') .
                '/members/' . $user->im_value;

            $guild_headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bot ' . Setting::obtain('discord_bot_token'),
            ];

            $guild_body = [
                'access_token' => $access_token,
            ];

            $client->put($discord_guild_url, [
                'headers' => $guild_headers,
                'json' => $guild_body,
            ]);
        }

        return $response->withRedirect($_ENV['baseUrl'] . '/user/edit');
    }

    public function telegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user_auth = json_decode($request->getParam('user'), true);

        $check_hash = $user_auth['hash'];
        unset($user_auth['hash']);
        $data_check_arr = [];

        foreach ($user_auth as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }

        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', Setting::obtain('telegram_token'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0 || (time() - $user_auth['auth_date']) > 86400) {
            return ResponseHelper::error($response, '绑定失败');
        }

        $antiXss = new AntiXSS();

        $telegram_id = $antiXss->xss_clean($user_auth['id']);
        $user = $this->user;

        if (User::where('im_type', 4)->where('im_value', $telegram_id)->first() !== null ||
            ($user->im_type === 4 && $user->im_value === $telegram_id)) {
            return ResponseHelper::error($response, 'Telegram 账户已绑定');
        }

        $user->im_type = 4;
        $user->im_value = $telegram_id;

        $user->save();

        return ResponseHelper::success($response, '绑定成功');
    }
}
