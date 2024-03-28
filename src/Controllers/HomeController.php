<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Auth;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Smarty\Exception as SmartyException;

final class HomeController extends BaseController
{
    /**
     * @throws SmartyException
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('index.tpl'));
    }

    /**
     * @throws SmartyException
     */
    public function tos(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    /**
     * @throws SmartyException
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
     * @throws SmartyException
     */
    public function notFound(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    /**
     * @throws SmartyException
     */
    public function methodNotAllowed(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    /**
     * @throws SmartyException
     */
    public function internalServerError(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }
}
