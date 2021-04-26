<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\{
    Code,
    User
};
use App\Utils\{
    Tools,
    DatatablesHelper
};
use App\Services\Auth;
use Ozdemir\Datatables\Datatables;
use Slim\Http\{
    Request,
    Response
};

class CodeController extends AdminController
{
    /**
     * 后台充值码及充值记录页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'id'          => 'ID',
            'code'        => '内容',
            'type'        => '类型',
            'number'      => '操作',
            'isused'      => '是否已经使用',
            'userid'      => '用户ID',
            'user_name'   => '用户名',
            'usedatetime' => '使用时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'code/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/code/index.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function create($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/code/add.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function donate_create($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/code/add_donate.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function add($request, $response, $args)
    {
        $n      = $request->getParam('amount');
        $number = $request->getParam('number');

        if (Tools::isInt($n) == false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法请求'
            ]);
        }

        for ($i = 0; $i < $n; $i++) {
            $char              = Tools::genRandomChar(32);
            $code              = new Code();
            $code->code        = time() . $char;
            $code->type        = -1;
            $code->number      = $number;
            $code->userid      = 0;
            $code->usedatetime = '1989:06:04 02:30:00';
            $code->save();
        }

        $rs['ret'] = 1;
        $rs['msg'] = '充值码添加成功';
        return $response->withJson($rs);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
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
        $code->usedatetime = date('Y:m:d H:i:s');

        $code->save();

        $rs['ret'] = 1;
        $rs['msg'] = '添加成功';
        return $response->withJson($rs);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax_code($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select code.id,code.code,code.type,code.number,code.isused,code.userid,code.userid as user_name,code.usedatetime from code');

        $datatables->edit('number', static function ($data) {
            switch ($data['type']) {
                case -1:
                    return '充值 ' . $data['number'] . ' 元';

                case -2:
                    return '支出 ' . $data['number'] . ' 元';

                default:
                    return '已经废弃';
            }
        });

        $datatables->edit('isused', static function ($data) {
            return $data['isused'] == 1 ? '已使用' : '未使用';
        });

        $datatables->edit('userid', static function ($data) {
            return $data['userid'] == 0 ? '未使用' : $data['userid'];
        });

        $datatables->edit('user_name', static function ($data) {
            $user = User::find($data['user_name']);
            if ($user == null) {
                return '未使用';
            }

            return $user->user_name;
        });

        $datatables->edit('type', static function ($data) {
            switch ($data['type']) {
                case -1:
                    return '充值金额';

                case -2:
                    return '财务支出';

                default:
                    return '已经废弃';
            }
        });

        $datatables->edit('usedatetime', static function ($data) {
            return $data['usedatetime'] > '2000-1-1 0:0:0' ? $data['usedatetime'] : '未使用';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
