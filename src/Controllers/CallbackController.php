<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Setting;
use App\Services\Bot\Telegram\Process;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use SmartyException;
use Telegram\Bot\Exceptions\TelegramSDKException;

/**
 *  CallbackController
 */
final class CallbackController extends BaseController
{
    /**
     * @throws InvalidDatabaseException
     * @throws SmartyException
     * @throws TelegramSDKException
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return match ($args['type']) {
            'telegram' => $this->telegram($request, $response, $args),
            default => $response->withStatus(404)->write($this->view()->fetch('404.tpl')),
        };
    }

    /**
     * @throws TelegramSDKException
     * @throws InvalidDatabaseException
     */
    public function telegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');

        if (Setting::obtain('enable_telegram') && $token === Setting::obtain('telegram_request_token')) {
            Process::index($request);
            $result = '1';
        } else {
            $result = '0';
        }

        return $response->write($result);
    }
}
