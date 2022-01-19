<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Code;
use App\Models\Setting;
use App\Utils\Tools;
use App\Services\Auth;
use App\Services\Mail;
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
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
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
    public function ajax_code($request, $response, $args)
    {
        $query = Code::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
            }
        );

        $data  = [];
        foreach ($query['datas'] as $value) {
            /** @var Code $value */
            /** 充值记录作为对账，用户不存在也不应删除 */
            $tempdata                = [];
            $tempdata['id']          = $value->id;
            $tempdata['code']        = $value->code;
            $tempdata['type']        = $value->type();
            $tempdata['number']      = $value->number();
            $tempdata['isused']      = $value->isused();
            $tempdata['userid']      = $value->userid();
            $tempdata['user_name']   = $value->user_name();
            $tempdata['usedatetime'] = $value->usedatetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => Code::count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
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
        $cards       = [];
        $user        = Auth::getUser();
        $amount      = $request->getParam('amount');
        $face_value  = $request->getParam('face_value');
        $code_length = $request->getParam('code_length');

        if (Tools::isInt($amount) == false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填写充值码生成数量'
            ]);
        }

        if ($face_value == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填写充值码面额'
            ]);
        }

        for ($i = 0; $i < $amount; $i++) {
            // save array
            $recharge_code     = Tools::genRandomChar($code_length);
            array_push($cards, $recharge_code);
            // save database
            $code              = new Code();
            $code->code        = $recharge_code;
            $code->type        = -1;
            $code->number      = $face_value;
            $code->userid      = 0;
            $code->usedatetime = '1989:06:04 02:30:00';
            $code->save();
        }

        if (Setting::obtain('mail_driver') != 'none') {
            Mail::send(
                $user->email,
                $_ENV['appName'] . '- 充值码',
                'giftcard.tpl',
                [
                    'text' => implode('<br/>', $cards)
                ],
                []
            );
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
}
