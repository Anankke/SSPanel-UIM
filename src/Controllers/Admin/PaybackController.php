<?php
namespace App\Controllers\Admin;

use App\Models\Payback;

class PaybackController extends UserController
{
    public static function page(){
        $details = [
            'route' => 'payback',
            'title' => [
                'title' => '返利记录',
                'subtitle' => '邀请注册的用户返利给邀请人的记录',
            ],
            'field' => [
                'id' => '#',
                'total' => '订单金额',
                'userid' => '订单用户',
                'ref_by' => '邀请人',
                'ref_get' => '返利金额',
                'fraud_detect' => '是否欺诈',
                'associated_order' => '关联订单',
                'datetime' => '返利时间'
            ],
            'search_dialog' => [
                [
                    'id' => 'userid',
                    'info' => '订单用户',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'ref_by',
                    'info' => '邀请人',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'ref_get',
                    'info' => '返利金额',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false,
                ],
                [
                    'id' => 'associated_order',
                    'info' => '关联订单',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'fraud_detect',
                    'info' => '是否欺诈',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '通过',
                        '1' => '欺诈',
                    ],
                    'exact' => true,
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = Payback::orderBy('id', 'desc')
        ->limit(500)
        ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/payback.tpl')
        );
    }

    public function ajaxQuery($request, $response, $args)
    {
        $condition = [];
        $details = self::page();
        foreach ($details['search_dialog'] as $from)
        {
            $field = $from['id'];
            $keyword = $request->getParam($field);
            if ($from['type'] == 'input') {
                if ($from['exact']) {
                    ($keyword != '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword != '') && array_push($condition, [$field, 'like', '%'.$keyword.'%']);
                }
            }
            if ($from['type'] == 'select') {
                ($keyword != 'all') && array_push($condition, [$field, '=', $keyword]);
            }
        }

        $results = Payback::orderBy('id', 'desc')
        ->where($condition)
        ->limit(500)
        ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        Payback::find($item_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }
}
