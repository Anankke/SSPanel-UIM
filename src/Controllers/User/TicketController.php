<?php
namespace App\Controllers\User;

use App\Models\WorkOrder;
use App\Models\User;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface;

class TicketController extends UserController
{
    public function ticket($request, $response, $args): ?ResponseInterface
    {
        if ($_ENV['enable_ticket'] = false) {
            return null;
        }

        $tickets = WorkOrder::where('user_id', $this->user->id)
        ->where('is_topic', 1)
        ->orderBy('id', 'desc')
        ->limit(20)
        ->get();

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->display('user/ticket/index.tpl')
        );
    }

    public function ticket_create($request, $response, $args)
    {
        return $response->write(
            $this->view()
                ->display('user/ticket/create.tpl')
        );
    }

    public function ticket_add($request, $response, $args)
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');

        try {
            if ($title == '') {
                throw new \Exception('请填写工单标题');
            }
            if ($content == '') {
                throw new \Exception('请填写工单内容');
            }
            if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
                throw new \Exception('工单内容不能包含关键词 admin 和 user');
            }

            $last_tk_id = WorkOrder::where('is_topic', 1)->orderBy('id', 'desc')->first();

            $anti_xss = new AntiXSS();
            $ticket = new WorkOrder;
            $ticket->tk_id = (empty($last_tk_id)) ? 1 : $last_tk_id->tk_id + 1;
            $ticket->is_topic = 1;
            $ticket->title = $anti_xss->xss_clean($title);
            $ticket->content = $anti_xss->xss_clean($content);
            $ticket->user_id = $this->user->id;
            $ticket->created_at = time();
            $ticket->updated_at = time();
            $ticket->closed_at = null;
            $ticket->closed_by = null;
            $ticket->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        if ($_ENV['mail_ticket']) {
            $admins = User::where('is_admin', 1)->get();
            foreach ($admins as $admin) {
                $admin->sendMail($_ENV['appName'] . ' - 新的工单', 'news/warn.tpl',
                    [
                        'text' => '新工单开启：' . $anti_xss->xss_clean($title)
                    ], []
                );
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '新工单已创建'
        ]);
    }

    public function ticket_update($request, $response, $args)
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
            if ($topic->user_id != $this->user->id) {
                throw new \Exception('此主题帖不属于你');
            }
            if ($topic->closed_by == '已关闭') {
                throw new \Exception('此主题帖已关闭，如有需要请创建新工单');
            }
            $content = $request->getParam('content');
            if ($content == '') {
                throw new \Exception('请添加回复内容');
            }
            if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
                throw new \Exception('回复内容不能包含关键词 admin 和 user');
            }

            $anti_xss = new AntiXSS();
            $ticket = new WorkOrder;
            $ticket->tk_id = $tk_id;
            $ticket->is_topic = 0;
            $ticket->title = null;
            $ticket->content = $anti_xss->xss_clean($content);
            $ticket->user_id = $this->user->id;
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
            $admins = User::where('is_admin', 1)->get();
            foreach ($admins as $admin) {
                $admin->sendMail($_ENV['appName'] . ' - 用户工单回复', 'news/warn.tpl',
                    [
                        'text' => '工单主题：' . $anti_xss->xss_clean($topic->title) .
                        '<br/>' . '新添回复：' . $anti_xss->xss_clean($content)
                    ], []
                );
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '回复成功'
        ]);
    }

    public function ticket_view($request, $response, $args)
    {
        $tk_id = $args['id'];
        $topic = WorkOrder::where('tk_id', $tk_id)
        ->where('is_topic', '1')
        ->first();

        if ($topic == null || $topic->user_id != $this->user->id) {
            // 避免平级越权
            return null;
        }

        $discussions = WorkOrder::where('tk_id', $tk_id)->get();

        return $response->write(
            $this->view()
                ->assign('topic', $topic)
                ->assign('discussions', $discussions)
                ->display('user/ticket/read.tpl')
        );
    }
}
