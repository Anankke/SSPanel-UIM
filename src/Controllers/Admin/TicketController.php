<?php
namespace App\Controllers\Admin;

use App\Services\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Utils\Tools;
use App\Services\Mail;
use App\Models\User;
use App\Models\Setting;
use App\Models\WorkOrder;
use voku\helper\AntiXSS;
use App\Controllers\AdminController;

class TicketController extends AdminController
{
    public static function page(){
        $details = [
            'route' => 'ticket',
            'title' => [
                'title' => '工单列表',
                'subtitle' => '所有用户提交的工单',
            ],
            'field' => [
                'tk_id' => '#',
                'title' => '主题',
                'user_id' => '提交用户',
                'created_at' => '创建时间',
                'updated_at' => '更新时间',
                'closed_at' => '关闭时间',
                'closed_by' => '状态'
            ],
            'search_dialog' => [
                [
                    'id' => 'user_id',
                    'info' => '提交用户',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => true, // 精确匹配; false 时模糊匹配
                ],
                [
                    'id' => 'title',
                    'info' => '工单主题',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false,
                ],
                [
                    'id' => 'content',
                    'info' => '工单回复内容',
                    'type' => 'input',
                    'placeholder' => '请输入',
                    'exact' => false,
                ],
                [
                    'id' => 'closed_by',
                    'info' => '工单状态',
                    'type' => 'select',
                    'select' => [
                        'all' => '所有状态',
                        'admin' => '被管理员关闭',
                        'system' => '被系统关闭',
                    ],
                    'exact' => true,
                ],
            ],
        ];

        return $details;
    }

    public function index($request, $response, $args)
    {
        $logs = WorkOrder::where('is_topic', 1)
        ->orderBy('id', 'desc')
        ->get();

        return $response->write(
            $this->view()
                ->assign('logs', $logs)
                ->assign('details', self::page())
                ->display('admin/ticket/index.tpl')
        );
    }

    public function read($request, $response, $args)
    {
        $tk_id = $args['id'];
        $topic = WorkOrder::where('tk_id', $tk_id)
        ->where('is_topic', '1')
        ->first();

        if ($topic == null) {
            return null;
        }

        $discussions = WorkOrder::where('tk_id', $tk_id)->get();

        return $response->write(
            $this->view()
                ->assign('topic', $topic)
                ->assign('discussions', $discussions)
                ->display('admin/ticket/read.tpl')
        );
    }

    public function addReply($request, $response, $args)
    {
        try {
            $tk_id = $args['id'];
            $ticket = WorkOrder::where('tk_id', $tk_id)->first();
            if ($ticket == null) {
                throw new \Exception('回复的主题帖不存在');
            }
            $topic = WorkOrder::where('tk_id', $tk_id)
            ->where('is_topic', '1')
            ->first();
            if ($topic->closed_by == '已关闭') {
                throw new \Exception('此主题帖已关闭');
            }
            $content = $request->getParam('content');
            if ($content == '') {
                throw new \Exception('请添加回复内容');
            }

            $anti_xss = new AntiXSS();
            $ticket = new WorkOrder;
            $ticket->tk_id = $tk_id;
            $ticket->is_topic = 0;
            $ticket->title = null;
            $ticket->content = $anti_xss->xss_clean($content);
            $ticket->user_id = 0; // 管理员的回复设为0
            $ticket->created_at = time();
            $ticket->updated_at = time();
            $ticket->closed_at = null;
            $ticket->closed_by = null;
            $ticket->save();

            $topic->updated_at = time();
            $topic->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        if ($_ENV['mail_ticket']) {
            $anti_xss = new AntiXSS();
            $user = User::find($topic->user_id);
            $user->sendMail($_ENV['appName'] . ' - 工单被回复', 'news/warn.tpl',
                [
                    'text' => '工单主题：' . $anti_xss->xss_clean($topic->title) .
                    '<br/>' . '新添回复：' . $anti_xss->xss_clean($content)
                ], []
            );
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '回复成功'
        ]);
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

        $results = WorkOrder::orderBy('id', 'desc')
        ->where($condition)
        ->limit(500)
        ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results
        ]);
    }

    public function closeTk($request, $response, $args)
    {
        $item_id = $args['id'];
        $ticket = WorkOrder::where('is_topic', '1')
        ->where('tk_id', $item_id)
        ->first();
        if ($ticket->closed_by == '已关闭') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '工单已是关闭状态'
            ]);
        }

        $ticket->closed_at = time();
        $ticket->closed_by = 'admin';
        $ticket->save();

        if ($_ENV['mail_ticket']) {
            $anti_xss = new AntiXSS();
            $user = User::find($ticket->user_id);
            $user->sendMail($_ENV['appName'] . ' - 工单被关闭', 'news/warn.tpl',
                [
                    'text' => '工单主题：' . $anti_xss->xss_clean($ticket->title)
                ], []
            );
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '已关闭此工单'
        ]);
    }

    public function delete($request, $response, $args)
    {
        $item_id = $args['id'];
        WorkOrder::where('tk_id', $item_id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功'
        ]);
    }
}
