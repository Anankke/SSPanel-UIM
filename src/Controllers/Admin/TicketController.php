<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ticket;
use App\Models\User;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function array_merge;
use function count;
use function json_decode;
use function json_encode;
use function time;

final class TicketController extends BaseController
{
    public static array $details =
    [
        'field' => [
            'op' => '操作',
            'id' => '工单ID',
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
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/ticket/index.tpl')
        );
    }

    /**
     * 后台更新工单内容
     */
    public function update(ServerRequest $request, Response $response, array $args)
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

        $content_old = json_decode($ticket->content, true);
        $content_new = [
            [
                'comment_id' => $content_old[count($content_old) - 1]['comment_id'] + 1,
                'commenter_name' => 'Admin',
                'comment' => $antiXss->xss_clean($comment),
                'datetime' => time(),
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

        $ticket->content = json_encode(array_merge($content_old, $content_new));
        $ticket->status = 'open_wait_user';
        $ticket->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功',
        ]);
    }

    /**
     * 后台查看指定工单
     *
     * @throws Exception
     */
    public function ticketView(ServerRequest $request, Response $response, array $args)
    {
        $id = $args['id'];
        $ticket = Ticket::where('id', '=', $id)->first();
        $comments = json_decode($ticket->content, true);

        if ($ticket === null) {
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }

        return $response->write(
            $this->view()
                ->assign('ticket', $ticket)
                ->assign('comments', $comments)
                ->registerClass('Tools', Tools::class)
                ->fetch('admin/ticket/view.tpl')
        );
    }

    /**
     * 后台关闭工单
     */
    public function close(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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
     * 后台删除工单
     */
    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = $args['id'];
        Ticket::where('id', '=', $id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    /**
     * 后台工单页面 Ajax
     */
    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $tickets = Ticket::orderBy('id', 'desc')->get();

        foreach ($tickets as $ticket) {
            $ticket->op = '<button type="button" class="btn btn-red" id="delete-ticket" 
            onclick="deleteTicket(' . $ticket->id . ')">删除</button>
            <button type="button" class="btn btn-orange" id="close-ticket" 
            onclick="closeTicket(' . $ticket->id . ')">关闭</button>
            <a class="btn btn-blue" href="/admin/ticket/' . $ticket->id . '/view">查看</a>';
            $ticket->status = Tools::getTicketStatus($ticket);
            $ticket->type = Tools::getTicketType($ticket);
            $ticket->datetime = Tools::toDateTime((int) $ticket->datetime);
        }

        return $response->withJson([
            'tickets' => $tickets,
        ]);
    }
}
