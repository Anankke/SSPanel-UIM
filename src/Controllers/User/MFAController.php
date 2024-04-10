<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Services\MFA;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

/**
 *  MFAController
 */
final class MFAController extends BaseController
{
    public function checkGa(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $code = $request->getParam('code');

        if ($code === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '二维码不能为空',
            ]);
        }

        if (! MFA::verifyGa($this->user, $code)) {
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

    public function setGa(ServerRequest $request, Response $response, array $args): ResponseInterface
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

        if ($user->save()) {
            return $response->withJson([
                'ret' => 1,
                'msg' => '设置成功',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => '设置失败',
        ]);
    }

    /**
     * @throws Exception
     */
    public function resetGa(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->ga_token = MFA::generateGaToken();

        if ($user->save()) {
            return $response->withJson([
                'ret' => 1,
                'msg' => '重置成功',
                'data' => [
                    'ga-token' => $user->ga_token,
                    'ga-url' => MFA::getGaUrl($user),
                ],
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => '重置失败',
        ]);
    }
}
