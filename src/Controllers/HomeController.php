<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InviteCode;
use App\Utils\Telegram\Process;
use App\Utils\TelegramProcess;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *  HomeController
 */
final class HomeController extends BaseController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('index.tpl'));
    }

    /**
     * @param array     $args
     */
    public function code(Request $request, Response $response, array $args): ResponseInterface
    {
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $response->write($this->view()->assign('codes', $codes)->fetch('code.tpl'));
    }

    /**
     * @param array     $args
     */
    public function tos(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    /**
     * @param array     $args
     */
    public function staff(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('staff.tpl'));
    }

    /**
     * @param array     $args
     */
    public function telegram(Request $request, Response $response, array $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');
        if ($token === $_ENV['telegram_request_token']) {
            if ($_ENV['use_new_telegram_bot']) {
                Process::index();
            } else {
                TelegramProcess::process();
            }
            $result = '1';
        } else {
            $result = '0';
        }
        return $response->write($result);
    }

    /**
     * @param array     $args
     */
    public function page404(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    /**
     * @param array     $args
     */
    public function page405(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    /**
     * @param array     $args
     */
    public function page500(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }
}
