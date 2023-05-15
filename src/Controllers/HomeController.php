<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Auth;
use App\Utils\Telegram\Process;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;

/**
 *  HomeController
 */
final class HomeController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('index.tpl'));
    }

    /**
     * @throws Exception
     */
    public function tos(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    /**
     * @throws Exception
     */
    public function staff(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = Auth::getUser();
        if (! $user->isLogin) {
            return $response->withStatus(404)->write($this->view()->fetch('404.tpl'));
        }

        return $response->write($this->view()->fetch('staff.tpl'));
    }

    /**
     * @throws Exception
     */
    public function notFound(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    /**
     * @throws Exception
     */
    public function methodNotAllowed(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    /**
     * @throws Exception
     */
    public function internalServerError(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }

    /**
     * @throws TelegramSDKException
     */
    public function telegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');

        if ($_ENV['enable_telegram'] && $token === $_ENV['telegram_request_token']) {
            Process::index($request);
            $result = '1';
        } else {
            $result = '0';
        }

        return $response->write($result);
    }
}
