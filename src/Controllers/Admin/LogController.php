<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Log;

class LogController extends AdminController
{
    public static function page()
    {
        $details = [
            'route' => 'log',
            'title' => [
                'title' => '日志中心',
                'subtitle' => '浏览和处理上报的日志',
            ],
            'field' => [
                'id' => '#',
                'type' => '分类',
                'reporter' => '上报者',
                'level' => '日志等级',
                'msg' => '日志正文',
                'status' => '处理状态',
                'created_at' => '创建时间',
            ],
            'search_dialog' => [
                [
                    'id' => 'type',
                    'info' => '分类',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'reporter',
                    'info' => '上报者',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true,
                ],
                [
                    'id' => 'msg',
                    'info' => '日志正文',
                    'type' => 'input',
                    'placeholder' => '模糊匹配',
                    'exact' => false,
                ],
                [
                    'id' => 'level',
                    'info' => '日志等级',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有等级',
                        'low' => '低',
                        'middle' => '中',
                        'high' => '高',
                    ],
                    'exact' => true,
                ],
                [
                    'id' => 'status',
                    'info' => '处理状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        '0' => '未处理',
                        '1' => '已处理',
                    ],
                    'exact' => true,
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = Log::orderBy('id', 'desc')
            ->limit(500)
            ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/log.tpl')
        );
    }

    public function update($request, $response, $args)
    {
        $item_id = $args['id'];
        $status = $request->getParam('status');

        $log = Log::find($item_id);
        if ($log->status == '已处理') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '此条日志已是已处理状态',
            ]);
        }

        $log->status = $status;
        $log->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '标记成功',
        ]);
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

        $results = Log::orderBy('id', 'desc')
            ->where($condition)
            ->limit(500)
            ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        Log::find($item_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }
}
