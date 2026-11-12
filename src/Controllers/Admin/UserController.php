<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AuthController;
use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\User;
use App\Services\I18n;
use App\Services\Wallet;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function bccomp;
use function bcround;
use function is_finite;
use function is_numeric;
use function ltrim;
use function sprintf;

final class UserController extends BaseController
{
    private static array $details = [
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

    private static array $update_field = [
        'email',
        'user_name',
        'pass',
        'money_adjust',
        'money_remark',
        'ref_by',
        'port',
        'method',
        'transfer_enable',
        'node_group',
        'class',
        'class_expire',
        'auto_reset_day',
        'auto_reset_bandwidth',
        'node_speedlimit',
        'node_iplimit',
        'locale',
        'banned_reason',
        'remark',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
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
    public function create(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $email = $request->getParam('email');
        $ref_by = $request->getParam('ref_by');
        $password = $request->getParam('password');
        $balance = $request->getParam('balance');

        if ($email === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱不能为空',
            ]);
        }

        $exist = (new User())->where('email', $email)->first();

        if ($exist !== null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱已存在',
            ]);
        }

        if ($password === '') {
            $password = Tools::genRandomChar(16);
        }

        (new AuthController())->registerHelper(
            $response,
            'user',
            $email,
            $password,
            '',
            0,
            '',
            $balance,
            1
        );
        $user = (new User())->where('email', $email)->first();

        if ($ref_by !== '') {
            $user->ref_by = (int) $ref_by;
            $user->save();
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功，用户邮箱：' . $email . ' 密码：'.$password,
        ]);
    }

    /**
     * @throws Exception
     */
    public function edit(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = (new User())->find($args['id']);
        $user->last_use_time = Tools::toDateTime($user->last_use_time);
        $user->last_check_in_time = Tools::toDateTime($user->last_check_in_time);
        $user->last_login_time = Tools::toDateTime($user->last_login_time);

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('edit_user', $user)
                ->assign('ss_methods', Tools::getSsMethod())
                ->assign('locales', I18n::getLocaleList())
                ->fetch('admin/user/edit.tpl')
        );
    }

    public function update(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $user = (new User())->find($id);

        $money_adjust = $request->getParam('money_adjust');
        $money_delta = '0.00';

        if ($money_adjust !== null && $money_adjust !== '') {
            if (! is_numeric($money_adjust) || ! is_finite((float) $money_adjust)) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '余额调整额格式错误',
                ]);
            }

            $money_delta = bcround(sprintf('%.10F', (float) $money_adjust), 2);
        }

        if (bccomp($money_delta, '0', 2) !== 0) {
            $money_remark = (string) $request->getParam('money_remark');

            try {
                if (bccomp($money_delta, '0', 2) > 0) {
                    Wallet::credit($id, $money_delta, $money_remark !== '' ? $money_remark : '管理员添加余额');
                } else {
                    Wallet::debit(
                        $id,
                        ltrim($money_delta, '-'),
                        $money_remark !== '' ? $money_remark : '管理员扣除余额'
                    );
                }
            } catch (LogicException) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '余额不足，无法扣除',
                ]);
            }
        }

        if ($request->getParam('pass') !== '' && $request->getParam('pass') !== null) {
            $user->pass = Hash::passwordHash($request->getParam('pass'));

            if (Config::obtain('enable_forced_replacement')) {
                $user->removeLink();
            }
        }

        $user->email = $request->getParam('email');
        $user->user_name = $request->getParam('user_name');
        $user->ref_by = $request->getParam('ref_by');
        $user->port = $request->getParam('port');
        $user->method = $request->getParam('method');
        $user->transfer_enable = Tools::autoBytesR($request->getParam('transfer_enable'));
        $user->node_group = $request->getParam('node_group');
        $user->class = $request->getParam('class');
        $user->class_expire = $request->getParam('class_expire');
        $user->auto_reset_day = $request->getParam('auto_reset_day');
        $user->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $user->node_speedlimit = $request->getParam('node_speedlimit');
        $user->node_iplimit = $request->getParam('node_iplimit');
        $user->locale = $request->getParam('locale');
        $user->is_admin = $request->getParam('is_admin') === 'true' ? 1 : 0;
        $user->is_shadow_banned = $request->getParam('is_shadow_banned') === 'true' ? 1 : 0;
        $user->is_banned = $request->getParam('is_banned') === 'true' ? 1 : 0;
        $user->banned_reason = $request->getParam('banned_reason');
        $user->remark = $request->getParam('remark');

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

    public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $user = (new User())->find((int) $id);

        if (! $user->kill()) {
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

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $users = (new User())->orderBy('id', 'desc')->get();

        foreach ($users as $user) {
            $user->op = '<button class="btn btn-red" id="delete-user-' . $user->id . '" 
            onclick="deleteUser(' . $user->id . ')">删除</button>
            <a class="btn btn-primary" href="/admin/user/' . $user->id . '/edit">编辑</a>';
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
