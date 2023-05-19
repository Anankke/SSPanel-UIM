<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Payback;
use App\Models\User;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function is_numeric;

final class InviteController extends BaseController
{
    public static array $details =
        [
            'field' => [
                'id' => '事件ID',
                'total' => '原始金额',
                'userid' => '发起用户ID',
                'user_name' => '发起用户名',
                'ref_by' => '获利用户ID',
                'ref_user_name' => '获利用户名',
                'ref_get' => '获利金额',
                'datetime' => '时间',
            ],
            'update_dialog' => [
                [
                    'id' => 'userid',
                    'info' => '修改的用户',
                    'type' => 'input',
                    'placeholder' => '需要修改邀请者的用户 ID 或 Email',
                ],
                [
                    'id' => 'refid',
                    'info' => '邀请者 ID',
                    'type' => 'input',
                    'placeholder' => '目标邀请者的用户 ID',
                ],
            ],
            'add_dialog' => [
                [
                    'id' => 'userid',
                    'info' => '修改的用户',
                    'type' => 'input',
                    'placeholder' => '需要邀请数量的用户 ID 或 Email',
                ],
                [
                    'id' => 'invite_num',
                    'info' => '邀请数量',
                    'type' => 'input',
                    'placeholder' => '需要添加的邀请数量',
                ],
            ],
        ];

    /**
     * 后台邀请记录页面
     *
     * @throws Exception
     */
    public function invite(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/invite.tpl')
        );
    }

    /**
     * 更改用户邀请者
     */
    public function update(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $userid = $request->getParam('userid');

        if ($userid === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '参数错误',
            ]);
        }

        if (str_contains($userid, '@')) {
            $user = User::where('email', '=', $userid)->first();
        } else {
            $user = User::find((int) $userid);
        }

        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请者更改失败，检查用户 ID/Email 是否输入正确',
            ]);
        }

        $user->ref_by = $request->getParam('refid', 0);  //如未提供，则删除用户的邀请者
        $user->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '邀请者更改成功',
        ]);
    }

    /**
     * 为用户添加邀请次数
     */
    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $invite_num = (int) $request->getParam('invite_num');

        if (! is_numeric($invite_num)) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请次数错误',
            ]);
        }

        if (str_contains($request->getParam('userid'), '@')) {
            $user = User::where('email', '=', $request->getParam('userid'))->first();
        } else {
            $user = User::find((int) $request->getParam('userid'));
        }

        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邀请次数添加失败，检查用户 ID/Email 是否输入正确',
            ]);
        }

        $user->addInviteNum($invite_num);

        return $response->withJson([
            'ret' => 1,
            'msg' => '邀请次数添加成功',
        ]);
    }

    /**
     * 后台登录记录页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $paybacks = Payback::orderBy('id', 'desc')->get();

        foreach ($paybacks as $payback) {
            $payback->datetime = Tools::toDateTime((int) $payback->datetime);
            $payback->user_name = $payback->user() === null ? '已注销' : $payback->user()->user_name;
            $payback->ref_user_name = $payback->refUser() === null ? '已注销' : $payback->refUser()->user_name;
        }

        return $response->withJson([
            'paybacks' => $paybacks,
        ]);
    }
}
