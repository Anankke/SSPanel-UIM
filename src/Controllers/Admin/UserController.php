<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AuthController;
use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UserMoneyLog;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function str_replace;
use const PHP_EOL;

final class UserController extends BaseController
{
    public static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '用户ID',
            'user_name' => '昵称',
            'email' => '邮箱',
            'money' => '余额',
            'ref_by' => '邀请人',
            'transfer_enable' => '流量限制',
            'transfer_used' => '当期用量',
            'class' => '等级',
            'is_admin' => '是否管理员',
            'is_banned' => '是否封禁',
            'is_inactive' => '是否闲置',
            'reg_date' => '注册时间',
            'class_expire' => '等级过期',
        ],
        'create_dialog' => [
            [
                'id' => 'email',
                'info' => '登录邮箱',
                'type' => 'input',
                'placeholder' => '',
            ],
            [
                'id' => 'password',
                'info' => '登录密码',
                'type' => 'input',
                'placeholder' => '留空则随机生成',
            ],
            [
                'id' => 'ref_by',
                'info' => '邀请人',
                'type' => 'input',
                'placeholder' => '邀请人的用户id，可留空',
            ],
            [
                'id' => 'balance',
                'info' => '账户余额',
                'type' => 'input',
                'placeholder' => '-1为按默认设置，其他为指定值',
            ],
        ],
    ];

    public static array $update_field = [
        'email',
        'user_name',
        'remark',
        'pass',
        'money',
        'is_admin',
        'ga_enable',
        'use_new_shop',
        'is_banned',
        'banned_reason',
        'transfer_enable',
        'invite_num',
        'ref_by',
        'class_expire',
        'expire_in',
        'node_group',
        'class',
        'auto_reset_day',
        'auto_reset_bandwidth',
        'node_speedlimit',
        'node_iplimit',
        'port',
        'passwd',
        'method',
        'forbidden_ip',
        'forbidden_port',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/user/index.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function createNewUser(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $email = $request->getParam('email');
        $ref_by = $request->getParam('ref_by');
        $password = $request->getParam('password');
        $balance = $request->getParam('balance');

        if ($email === '' || ! Tools::isEmailLegal($email)) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱格式错误',
            ]);
        }

        $exist = User::where('email', $email)->first();

        if ($exist !== null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱已存在',
            ]);
        }

        if ($password === '') {
            $password = Tools::genRandomChar(16);
        }

        AuthController::registerHelper($response, 'user', $email, $password, '', 1, '', 0, $balance, 1);
        $user = User::where('email', $email)->first();

        if ($ref_by !== '') {
            $user->ref_by = (int) $ref_by;
            $user->save();
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功，用户邮箱：'.$email.' 密码：'.$password,
        ]);
    }

    /**
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $user = User::find($args['id']);

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('edit_user', $user)
                ->fetch('admin/user/edit.tpl')
        );
    }

    public function update(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = (int) $args['id'];
        $user = User::find($id);

        if ($request->getParam('pass') !== '' && $request->getParam('pass') !== null) {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            $user->cleanLink();
        }

        if ($request->getParam('money') !== '' &&
            $request->getParam('money') !== null &&
            (float) $request->getParam('money') !== (float) $user->money
        ) {
            $money = (float) $request->getParam('money');
            $diff = $money - $user->money;
            $remark = ($diff > 0 ? '管理员添加余额' : '管理员扣除余额');
            (new UserMoneyLog())->addMoneyLog($id, (float) $user->money, $money, $diff, $remark);
            $user->money = $money;
        }

        $user->email = $request->getParam('email');
        $user->user_name = $request->getParam('user_name');
        $user->remark = $request->getParam('remark');
        $user->is_admin = $request->getParam('is_admin') === 'true' ? 1 : 0;
        $user->ga_enable = $request->getParam('ga_enable') === 'true' ? 1 : 0;
        $user->use_new_shop = $request->getParam('use_new_shop') === 'true' ? 1 : 0;
        $user->is_banned = $request->getParam('is_banned') === 'true' ? 1 : 0;
        $user->banned_reason = $request->getParam('banned_reason');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num = $request->getParam('invite_num');
        $user->ref_by = $request->getParam('ref_by');
        $user->class_expire = $request->getParam('class_expire');
        $user->expire_in = $request->getParam('expire_in');
        $user->node_group = $request->getParam('node_group');
        $user->class = $request->getParam('class');
        $user->auto_reset_day = $request->getParam('auto_reset_day');
        $user->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $user->node_speedlimit = $request->getParam('node_speedlimit');
        $user->node_iplimit = $request->getParam('node_iplimit');
        $user->port = $request->getParam('port');
        $user->passwd = $request->getParam('passwd');
        $user->method = $request->getParam('method');
        $user->forbidden_ip = str_replace(PHP_EOL, ',', $request->getParam('forbidden_ip'));
        $user->forbidden_port = str_replace(PHP_EOL, ',', $request->getParam('forbidden_port'));

        if (! $user->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = $args['id'];
        $user = User::find((int) $id);

        if (! $user->killUser()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $users = User::orderBy('id', 'desc')->get();

        foreach ($users as $user) {
            $user->op = '<button type="button" class="btn btn-red" id="delete-user-' . $user->id . '" 
            onclick="deleteUser(' . $user->id . ')">删除</button>
            <a class="btn btn-blue" href="/admin/user/' . $user->id . '/edit">编辑</a>';
            $user->transfer_enable = $user->enableTraffic();
            $user->transfer_used = $user->usedTraffic();
            $user->is_admin = $user->is_admin === 1 ? '是' : '否';
            $user->is_banned = $user->is_banned === 1 ? '是' : '否';
            $user->is_inactive = $user->is_inactive === 1 ? '是' : '否';
        }

        return $response->withJson([
            'users' => $users,
        ]);
    }
}
