<?php

namespace App\Controllers\Admin;

use App\Models\Code;
use App\Models\User;
use App\Controllers\AdminController;
use App\Utils\Tools;
use App\Services\Auth;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class CodeController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array("id" => "ID", "code" => "内容",
            "type" => "类型", "number" => "操作",
            "isused" => "是否已经使用", "userid" => "用户ID",
            "user_name" => "用户名", "usedatetime" => "使用时间");
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            array_push($table_config['default_show_column'], $column);
        }
        $table_config['ajax_url'] = 'code/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/code/index.tpl');
    }

    public function create($request, $response, $args)
    {
        return $this->view()->display('admin/code/add.tpl');
    }

    public function donate_create($request, $response, $args)
    {
        return $this->view()->display('admin/code/add_donate.tpl');
    }

    public function add($request, $response, $args)
    {
        $n = $request->getParam('amount');
        $type = $request->getParam('type');
        $number = $request->getParam('number');

        if (Tools::isInt($n) == false) {
            $rs['ret'] = 0;
            $rs['msg'] = "非法请求";
            return $response->getBody()->write(json_encode($rs));
        }

        for ($i = 0; $i < $n; $i++) {
            $char = Tools::genRandomChar(32);
            $code = new Code();
            $code->code = time() . $char;
            $code->type = -1;
            $code->number = $number;
            $code->userid = 0;
            $code->usedatetime = "1989:06:04 02:30:00";
            $code->save();
        }


        $rs['ret'] = 1;
        $rs['msg'] = "充值码添加成功";
        return $response->getBody()->write(json_encode($rs));
    }


    public function donate_add($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        $type = $request->getParam('type');
        $text = $request->getParam('code');

        $code = new Code();
        $code->code = $text;
        $code->type = $type;
        $code->number = $amount;
        $code->userid = Auth::getUser()->id;
        $code->isused = 1;
        $code->usedatetime = date("Y:m:d H:i:s");

        $code->save();

        $rs['ret'] = 1;
        $rs['msg'] = "添加成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax_code($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select code.id,code.code,code.type,code.number,code.isused,code.userid,code.userid as user_name,code.usedatetime from code');

        $datatables->edit('number', function ($data) {
            switch ($data['type']) {
                case -1:
                    return "充值 " . $data['number'] . " 元";

                case -2:
                    return "支出 " . $data['number'] . " 元";

                default:
                    return "已经废弃";
            }
        });

        $datatables->edit('isused', function ($data) {
            return $data['isused'] == 1 ? '已使用' : '未使用';
        });

        $datatables->edit('userid', function ($data) {
            return $data['userid'] == 0 ? '未使用' : $data['userid'];
        });

        $datatables->edit('user_name', function ($data) {
            $user = User::find($data['user_name']);
            if ($user == null) {
                return "未使用";
            }

            return $user->user_name;
        });

        $datatables->edit('type', function ($data) {
            switch ($data['type']) {
                case -1:
                    return "充值金额";

                case -2:
                    return "财务支出";

                default:
                    return "已经废弃";
            }
        });

        $datatables->edit('usedatetime', function ($data) {
            return $data['usedatetime'] > '2000-1-1 0:0:0' ? $data['usedatetime'] : "未使用";
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
