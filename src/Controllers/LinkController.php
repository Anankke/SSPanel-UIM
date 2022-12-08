<?php

declare(strict_types=1);

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\Link;
use App\Models\UserSubscribeLog;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *  LinkController
 */
final class LinkController extends BaseController
{
    public static function getContent(Request $request, Response $response, array $args): ResponseInterface
    {
        if (! $_ENV['Subscribe']) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $token = $args['token'];
        $params = $request->getQueryParams();

        $Elink = Link::where('token', $token)->first();
        if ($Elink === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $user = $Elink->getUser();
        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
            ]);
        }

        $sub_info = [];

        if (isset($params['clash']) && $params['clash'] === '1') {
            $sub_type = 'clash';
            $sub_info = SubController::getClash($user);
        }

        // 记录订阅日志
        if ($_ENV['subscribeLog'] === true) {
            UserSubscribeLog::addSubscribeLog($user, $sub_type, $request->getHeaderLine('User-Agent'));
        }

        $sub_details = ' upload=' . $user->u
            . '; download=' . $user->d
            . '; total=' . $user->transfer_enable
            . '; expire=' . strtotime($user->class_expire);

        return $response->withHeader('Subscription-Userinfo', $sub_details)->write(
            $sub_info
        );
    }
}
