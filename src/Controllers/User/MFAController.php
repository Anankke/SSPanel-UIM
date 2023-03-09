<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vectorface\GoogleAuthenticator;

/**
 *  MFAController
 */
final class MFAController extends BaseController
{
    public function checkGa(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $code = $request->getParam('code');

        if ($code === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '二维码不能为空',
            ]);
        }

        $user = $this->user;
        $ga = new GoogleAuthenticator();
        $rcode = $ga->verifyCode($user->ga_token, $code);

        if (! $rcode) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试错误',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试成功',
        ]);
    }

    public function setGa(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $enable = $request->getParam('enable');

        if ($enable === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '选项无效',
            ]);
        }

        $user = $this->user;
        $user->ga_enable = $enable;
        $user->save();
        return $response->withJson([
            'ret' => 1,
            'msg' => '设置成功',
        ]);
    }

    public function resetGa(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $ga = new GoogleAuthenticator();
        $secret = '';

        try {
            $secret = $ga->createSecret();
        } catch (Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '重置失败',
            ]);
        }

        $user = $this->user;
        $user->ga_token = $secret;

        if ($user->save()) {
            return $response->withJson([
                'ret' => 1,
                'msg' => '重置成功',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => '重置失败',
        ]);
    }
}
