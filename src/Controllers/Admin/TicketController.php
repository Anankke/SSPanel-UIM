<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ticket;
use App\Models\User;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

final class TicketController extends BaseController
{
    public static $details =
    [
        'field' => [
            'id' => 'ID',
            'title' => '主题',
            'status' => '工单状态',
            'type' => '工单类型',
            'userid' => '提交用户',
            'datetime' => '创建时间',
        ],
    ];

    /**
     * 后台工单页面
     *
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        $tickets = Ticket::orderBy('id', 'desc')->get();

        foreach ($tickets as $ticket) {
            $ticket->status = Tools::getTicketStatus($ticket->status);
            $ticket->type = Tools::getTicketType($ticket->type);
            $ticket->datetime = Tools::toDateTime($ticket->datetime);
        }

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->assign('details', self::$details)
                ->display('admin/ticket/index.tpl')
        );
    }

    /**
     * 后台 更新工单内容
     *
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $comment = $request->getParam('comment');

        if ($comment === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }

        $ticket = Ticket::where('id', $id)->first();

        if ($ticket === null) {
            return $response->withStatus(302)->withHeader('Location', '/admin/ticket');
        }

        $antiXss = new AntiXSS();

        $content_old = \json_decode($ticket->content, true);
        $content_new = [
            [
                'comment_id' => $content_old[count($content_old) - 1]['comment_id'] + 1,
                'commenter_name' => 'Admin',
                'comment' => $antiXss->xss_clean($comment),
                'datetime' => \time(),
            ],
        ];

        $user = User::find($ticket->userid);
        $user->sendMail(
            $_ENV['appName'] . '-工单被回复',
            'news/warn.tpl',
            [
                'text' => '您好，有人回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $ticket->id . '/view">工单</a>，请您查看。',
            ],
            []
        );

        $ticket->content = \json_encode(\array_merge($content_old, $content_new));
        $ticket->status = 'open_wait_user';
        $ticket->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功',
        ]);
    }

    /**
     * 后台 查看指定工单
     *
     * @param array     $args
     */
    public function ticketView(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $ticket = Ticket::where('id', '=', $id)->first();
        $comments = \json_decode($ticket->content, true);

        if ($ticket === null) {
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }

        return $response->write(
            $this->view()
                ->assign('ticket', $ticket)
                ->assign('comments', $comments)
                ->registerClass('Tools', Tools::class)
                ->display('admin/ticket/view.tpl')
        );
    }

    /**
     * 后台 关闭工单
     *
     * @param array     $args
     */
    public function close(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $ticket = Ticket::where('id', '=', $id)->first();

        if ($ticket->status === 'closed') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '工单已关闭',
            ]);
        }

        $user = User::find($ticket->userid);
        $user->sendMail(
            $_ENV['appName'] . '-工单已被关闭',
            'news/warn.tpl',
            [
                'text' => '您好，您的工单 #'. $ticket->id .' 已被关闭，如果您还有问题，欢迎提交新的工单。',
            ],
            []
        );

        $ticket->status = 'closed';
        $ticket->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '关闭成功',
        ]);
    }

    /**
     * 后台 删除工单
     *
     * @param array     $args
     */
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        Ticket::where('id', '=', $id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }
}
