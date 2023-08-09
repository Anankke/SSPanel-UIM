<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
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

    public function discord(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return ResponseHelper::error($response, '暂不支持');
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
