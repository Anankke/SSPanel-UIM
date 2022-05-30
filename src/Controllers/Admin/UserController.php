<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\Mail;
use App\Utils\Hash;
use App\Utils\Tools;

class UserController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'user',
            'title' => [
                'title' => '用户列表',
                'subtitle' => '系统中所有的注册用户，表格仅展示 500 条',
            ],
            'field' => [
                'id' => '#',
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
            'search_dialog' => [
                [
                    'id' => 'id',
                    'info' => '编号',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'user_name',
                    'info' => '昵称',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'email',
                    'info' => '邮箱',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'ref_by',
                    'info' => '邀请人',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'class',
                    'info' => '等级',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'enable',
                    'info' => '状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '禁用',
                        '1' => '启用',
                    ],
                    'exact' => true,
                ],
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
                    'placeholder' => '',
                ],
                [
                    'id' => 'email_notify',
                    'info' => '登录凭证',
                    'type' => 'select',
                    'select' => [
                        '1' => '发送登录凭证至新用户邮箱',
                        '0' => '不发送',
                    ],
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = User::orderBy('id', 'desc')
            ->limit(500)
            ->get();

        foreach ($logs as $log) {
            $log->transfer_enable = round($log->transfer_enable / 1073741824, 2);
            $log->last_day_t = round($log->last_day_t / 1073741824, 2);
        }

        $products = Product::where('type', '!=', 'other')->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->assign('products', $products)
                ->display('admin/user/index.tpl')
        );
    }

    public function ajaxQuery($request, $response, $args)
    {
        $condition = [];
        $details = self::page();
        foreach ($details['search_dialog'] as $from) {
            $field = $from['id'];
            $keyword = $request->getParam($field);
            if ($from['type'] == 'input') {
                if ($from['exact']) {
                    ($keyword != '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword != '') && array_push($condition, [$field, 'like', '%' . $keyword . '%']);
                }
            }
            if ($from['type'] == 'select') {
                ($keyword != 'all') && array_push($condition, [$field, '=', $keyword]);
            }
        }

        $results = User::orderBy('id', 'desc')
            ->where($condition)
            ->limit(500)
            ->get();

        foreach ($results as $result) {
            $result->transfer_enable = round($result->transfer_enable / 1073741824, 2);
            $result->last_day_t = round($result->last_day_t / 1073741824, 2);
        }

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        $user = User::find($item_id);

        if (!$user->kill_user()) {
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

    public function edit($request, $response, $args)
    {
        $user = User::find($args['id']);
        return $response->write(
            $this->view()
                ->assign('edit_user', $user)
                ->display('admin/user/edit.tpl')
        );
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $user->email = $request->getParam('email');
        $passwd = $request->getParam('passwd');
        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            $user->clean_link();
        }
        $origin_port = $user->port;
        $user->port = $request->getParam('port');
        $user->passwd = $request->getParam('passwd');
        $user->protocol = $request->getParam('protocol');
        $user->protocol_param = $request->getParam('protocol_param');
        $user->obfs = $request->getParam('obfs');
        $user->obfs_param = $request->getParam('obfs_param');
        $user->is_multi_user = $request->getParam('is_multi_user');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->node_speedlimit = $request->getParam('node_speedlimit');
        $user->node_connector = $request->getParam('node_connector');
        $user->enable = $request->getParam('enable');
        $user->is_admin = $request->getParam('is_admin');
        $user->ga_enable = $request->getParam('ga_enable');
        $user->node_group = $request->getParam('group');
        $user->ref_by = $request->getParam('ref_by');
        $user->remark = $request->getParam('remark');
        $user->user_name = $request->getParam('user_name');
        $user->money = $request->getParam('money');
        $user->class = $request->getParam('class');
        $user->class_expire = $request->getParam('class_expire');
        $user->expire_in = $request->getParam('expire_in');
        $user->forbidden_ip = str_replace(PHP_EOL, ',', $request->getParam('forbidden_ip'));
        $user->forbidden_port = str_replace(PHP_EOL, ',', $request->getParam('forbidden_port'));

        if (!$user->save()) {
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

    public function createNewUser($request, $response, $args)
    {
        $email = $request->getParam('email');
        $ref_by = $request->getParam('ref_by');
        $password = $request->getParam('password');
        $email_notify = $request->getParam('email_notify');
        $dispense_product = $request->getParam('dispense_product');

        try {
            if ($email == '') {
                throw new \Exception('请填写邮箱');
            }
            if (!Tools::emailCheck($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            $exist = User::where('email', $email)->first();
            if ($exist != null) {
                throw new \Exception('此邮箱已注册');
            }
            if ($password == '') {
                $password = Tools::genRandomChar(10);
            }
            if ($email_notify == '1') {
                if (Setting::obtain('mail_driver') == 'none') {
                    throw new \Exception('没有有效的发信配置');
                }
            }
            AuthController::register_helper('user', $email, $password, '', '1', '', 0, false, 'null');
            if ($email_notify == '1') {
                $subject = $_ENV['appName'] . ' - 您的账户已创建';
                $text = '请在 ' . $_ENV['baseUrl'] . ' 使用以下信息登录：'
                    . '<br/>账户：' . $email
                    . '<br/>密码：' . $password
                    . '<br/>'
                    . '<br/>建议您登录后前往 <b>资料编辑</b> 页面重新设定登录密码。如需帮助，可通过工单系统联系我们'
                    . '<br/>';
                Mail::send($email, $subject, 'newuser.tpl', [
                    'text' => $text,
                ], []);
            }
            if ($dispense_product != '0') {
                $user = User::where('email', $email)->first();
                $product = Product::find($dispense_product);
                $product_content = json_decode($product->content, true);
                foreach ($product_content as $key => $value) {
                    switch ($key) {
                        case 'product_time':
                            $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + ($value * 86400));
                            break;
                        case 'product_traffic':
                            $user->transfer_enable += $value * 1073741824;
                            break;
                        case 'product_class':
                            $user->class = $value;
                            break;
                        case 'product_class_time':
                            $user->class_expire = $user->expire_in;
                            break;
                        case 'product_speed':
                            $user->node_speedlimit = $value;
                            break;
                        case 'product_device':
                            $user->node_connector = $value;
                            break;
                    }
                }
                if ($ref_by != '') {
                    $user->ref_by = $ref_by;
                }
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
            'msg' => '添加成功',
        ]);
    }
}
