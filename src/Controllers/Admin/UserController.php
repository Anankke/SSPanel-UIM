<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AuthController;
use App\Controllers\BaseController;
use App\Models\Bought;
use App\Models\Shop;
use App\Models\User;
use App\Services\Auth;
use App\Utils\Check;
use App\Utils\Cookie;
use App\Utils\Hash;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

final class UserController extends BaseController
{
    public static $details = [
        'field' => [
            'op' => '操作',
            'id' => '用户ID',
            'user_name' => '昵称',
            'email' => '邮箱',
            'money' => '余额',
            'ref_by' => '邀请人',
            'transfer_enable' => '流量限制',
            'last_day_t' => '累计用量',
            'class' => '等级',
            'reg_date' => '注册时间',
            'expire_in' => '账户过期',
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

    public static $update_field = [
        'email',
        'user_name',
        'remark',
        'pass',
        'money',
        'is_admin',
        'is_banned',
        'banned_reason',
        'ga_enable',
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
        'node_connector',
        'port',
        'passwd',
        'method',
        'forbidden_ip',
        'forbidden_port',
    ];

    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('shops', Shop::orderBy('name')->get())
                ->assign('details', self::$details)
                ->display('admin/user/index.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function createNewUser(Request $request, Response $response, array $args)
    {
        $email = $request->getParam('email');
        $ref_by = $request->getParam('ref_by');
        $password = $request->getParam('password');
        $balance = $request->getParam('balance');
        $shop_id = $request->getParam('product');

        try {
            if ($email === '') {
                throw new \Exception('请填写邮箱');
            }
            if (!Check::isEmailLegal($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            $exist = User::where('email', $email)->first();
            if ($exist !== null) {
                throw new \Exception('此邮箱已注册');
            }
            if ($password === '') {
                $password = Tools::genRandomChar(16);
            }
            AuthController::registerHelper($response, 'user', $email, $password, '', 1, '', 0, $balance, 1);
            $user = User::where('email', $email)->first();
            if ($shop_id > 0) {
                $shop = Shop::find($shop_id);
                if ($shop !== null) {
                    $bought = new Bought();
                    $bought->userid = $user->id;
                    $bought->shopid = $shop->id;
                    $bought->datetime = \time();
                    $bought->renew = 0;
                    $bought->coupon = '';
                    $bought->price = $shop->price;
                    $bought->save();
                    $shop->buy($user);
                } else {
                    return $response->withJson([
                        'ret' => 0,
                        'msg' => '添加失败，套餐不存在',
                    ]);
                }
            }
            if ($ref_by !== '') {
                $user->ref_by = (int) $ref_by;
                $user->save();
            }
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功，用户邮箱：'.$email.' 密码：'.$password,
        ]);
    }

    /**
     * @param array     $args
     */
    public function edit(Request $request, Response $response, array $args)
    {
        $user = User::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('edit_user', $user)
                ->display('admin/user/edit.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        if ($request->getParam('pass') !== '' && $request->getParam('pass') !== null) {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            $user->cleanLink();
        }

        $user->addMoneyLog($request->getParam('money') - $user->money);

        $user->email = $request->getParam('email');
        $user->user_name = $request->getParam('user_name');
        $user->remark = $request->getParam('remark');
        $user->money = $request->getParam('money');
        $user->is_admin = $request->getParam('is_admin') === 'true' ? 1 : 0;
        $user->is_banned = $request->getParam('is_banned') === 'true' ? 1 : 0;
        $user->banned_reason = $request->getParam('banned_reason');
        $user->ga_enable = $request->getParam('ga_enable') === 'true' ? 1 : 0;
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
        $user->node_connector = $request->getParam('node_connector');
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
    /**
     * @param array     $args
     */
    public function delete(Request $request, Response $response, array $args)
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

    /**
     * @param array     $args
     */
    public function changetouser(Request $request, Response $response, array $args)
    {
        $userid = $request->getParam('userid');
        $adminid = $request->getParam('adminid');
        $user = User::find($userid);
        $admin = User::find($adminid);
        $expire_in = \time() + 60 * 60;

        if (! $admin->is_admin || ! $user || ! Auth::getUser()->isLogin) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法请求',
            ]);
        }

        Cookie::set([
            'uid' => $user->id,
            'email' => $user->email,
            'key' => Hash::cookieHash($user->pass, $expire_in),
            'ip' => md5($_SERVER['REMOTE_ADDR'] . $_ENV['key'] . $user->id . $expire_in),
            'expire_in' => $expire_in,
            'old_uid' => Cookie::get('uid'),
            'old_email' => Cookie::get('email'),
            'old_key' => Cookie::get('key'),
            'old_ip' => Cookie::get('ip'),
            'old_expire_in' => Cookie::get('expire_in'),
            'old_local' => $request->getParam('local'),
        ], $expire_in);

        return $response->withJson([
            'ret' => 1,
            'msg' => '切换成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args)
    {
        $users = User::orderBy('id', 'desc')->get();

        foreach ($users as $user) {
            $user->op = '<button type="button" class="btn btn-red" id="delete-user-' . $user->id . '" 
            onclick="deleteUser(' . $user->id . ')">删除</button>
            <a class="btn btn-blue" href="/admin/user/' . $user->id . '/edit">编辑</a>';
            $user->transfer_enable = round($user->transfer_enable / 1073741824, 2);
            $user->last_day_t = round($user->last_day_t / 1073741824, 2);
        }

        return $response->withJson([
            'users' => $users,
        ]);
    }
}
