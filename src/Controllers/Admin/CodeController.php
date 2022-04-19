<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Code;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\Mail;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

final class CodeController extends BaseController
{
    /**
     * 后台充值码及充值记录页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'code' => '内容',
                    'type' => '类型',
                    'number' => '操作',
                    'isused' => '是否已经使用',
                    'userid' => '用户ID',
                    'user_name' => '用户名',
                    'usedatetime' => '使用时间',
                ], 'code/ajax'))
                ->display('admin/code/index.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajaxCode(Request $request, Response $response, array $args)
    {
        $query = Code::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var Code $value */
            /** 充值记录作为对账，用户不存在也不应删除 */
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['code'] = $value->code;
            $tempdata['type'] = $value->type();
            $tempdata['number'] = $value->number();
            $tempdata['isused'] = $value->isused();
            $tempdata['userid'] = $value->userid();
            $tempdata['user_name'] = $value->userName();
            $tempdata['usedatetime'] = $value->usedatetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Code::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }

    /**
     * @param array     $args
     */
    public function create(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/code/add.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function donateCreate(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/code/add_donate.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args)
    {
        $cards = [];
        $user = Auth::getUser();
        $amount = $request->getParam('amount');
        $face_value = $request->getParam('face_value');
        $code_length = $request->getParam('code_length');

        if (Tools::isInt($amount) === false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填写充值码生成数量',
            ]);
        }

        if ($face_value === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填写充值码面额',
            ]);
        }

        for ($i = 0; $i < $amount; $i++) {
            // save array
            $recharge_code = Tools::genRandomChar($code_length);
            array_push($cards, $recharge_code);
            // save database
            $code = new Code();
            $code->code = $recharge_code;
            $code->type = -1;
            $code->number = $face_value;
            $code->userid = 0;
            $code->usedatetime = '1989:06:04 02:30:00';
            $code->save();
        }

        if (Setting::obtain('mail_driver') !== 'none') {
            Mail::send(
                $user->email,
                $_ENV['appName'] . '- 充值码',
                'giftcard.tpl',
                [
                    'text' => implode('<br/>', $cards),
                ],
                []
            );
        }

        return ResponseHelper::successfully($response, '充值码添加成功');
    }

    /**
     * @param array     $args
     */
    public function donateAdd(Request $request, Response $response, array $args)
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

        return ResponseHelper::successfully($response, '添加成功');
    }
}
