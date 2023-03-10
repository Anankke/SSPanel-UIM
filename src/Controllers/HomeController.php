<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InviteCode;
use App\Services\Auth;
use App\Utils\Telegram\Process;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

/**
 *  HomeController
 */
final class HomeController extends BaseController
{
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('index.tpl'));
    }

    public function code(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $response->write($this->view()->assign('codes', $codes)->fetch('code.tpl'));
    }

    public function tos(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    public function staff(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = Auth::getUser();
        if (! $user->isLogin) {
            return $response->withStatus(404)->write($this->view()->fetch('404.tpl'));
        }
        return $response->write($this->view()->fetch('staff.tpl'));
    }

    public function telegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');
        if ($token === $_ENV['telegram_request_token']) {
            Process::index();
            $result = '1';
        } else {
            $result = '0';
        }
        return $response->write($result);
    }

    public function page404(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    public function page405(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    public function page500(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }
}
