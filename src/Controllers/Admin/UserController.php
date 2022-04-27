<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\User;
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

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
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
}
