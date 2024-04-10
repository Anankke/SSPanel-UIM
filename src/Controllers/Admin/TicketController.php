<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Ticket;
use App\Models\User;
use App\Services\LLM;
use App\Services\Notification;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function array_merge;
use function count;
use function json_decode;
use function json_encode;
use function time;

final class TicketController extends BaseController
{
    private static array $details =
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

    private static string $err_msg = '请求失败';

    /**
     * 后台工单页面
     *
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/ticket/index.tpl')
        );
    }

    /**
     * @throws TelegramSDKException
     * @throws GuzzleException
     * @throws ClientExceptionInterface
     */
    public function update(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $comment = $request->getParam('comment') ?? '';

        if ($comment === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }

        $ticket = (new Ticket())->where('id', $id)->first();

        if ($ticket === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }

        $content_old = json_decode($ticket->content, true);
        $content_new = [
            [
                'comment_id' => $content_old[count($content_old) - 1]['comment_id'] + 1,
                'commenter_name' => 'Admin',
                'comment' => $comment,
                'datetime' => time(),
            ],
        ];

        $user = (new User())->find($ticket->userid);

        Notification::notifyUser(
            $user,
            $_ENV['appName'] . '-工单被回复',
            '你好，有人回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $ticket->id . '/view">工单</a>，请你查看。'
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
     * @throws GuzzleException
     * @throws TelegramSDKException
     * @throws ClientExceptionInterface
     */
    public function updateAI(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $ticket = (new Ticket())->where('id', $id)->first();

        if ($ticket === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }

        $content_old = json_decode($ticket->content, true);
        // 获取用户的第一个问题，作为 LLM 的输入
        $ai_reply = LLM::genTextResponse($content_old[0]['comment']);
        $content_new = [
            [
                'comment_id' => $content_old[count($content_old) - 1]['comment_id'] + 1,
                'commenter_name' => 'AI Admin',
                'comment' => $ai_reply,
                'datetime' => time(),
            ],
        ];

        $user = (new User())->find($ticket->userid);

        Notification::notifyUser(
            $user,
            $_ENV['appName'] . '-工单被回复',
            '你好，AI 回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $ticket->id . '/view">工单</a>，请你查看。'
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
    public function detail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $ticket = (new Ticket())->where('id', '=', $id)->first();

        if ($ticket === null) {
            return $response->withRedirect('/admin/ticket');
        }

        $comments = json_decode($ticket->content);

        foreach ($comments as $comment) {
            $comment->datetime = Tools::toDateTime((int) $comment->datetime);
        }

        return $response->write(
            $this->view()
                ->assign('ticket', $ticket)
                ->assign('comments', $comments)
                ->fetch('admin/ticket/view.tpl')
        );
    }

    /**
     * 后台关闭工单
     */
    public function close(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $ticket = (new Ticket())->where('id', '=', $id)->first();

        if ($ticket === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }

        if ($ticket->status === 'closed') {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }

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
    public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        (new Ticket())->where('id', '=', $id)->delete();

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    /**
     * 后台工单页面 Ajax
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $tickets = (new Ticket())->orderBy('id', 'desc')->get();

        foreach ($tickets as $ticket) {
            $ticket->op = '<button type="button" class="btn btn-red" id="delete-ticket" 
            onclick="deleteTicket(' . $ticket->id . ')">删除</button>';

            if ($ticket->status !== 'closed') {
                $ticket->op .= '
                <button type="button" class="btn btn-orange" id="close-ticket" 
                onclick="closeTicket(' . $ticket->id . ')">关闭</button>';
            }

            $ticket->op .= '
            <a class="btn btn-blue" href="/admin/ticket/' . $ticket->id . '/view">查看</a>';
            $ticket->status = $ticket->status();
            $ticket->type = $ticket->type();
            $ticket->datetime = Tools::toDateTime((int) $ticket->datetime);
        }

        return $response->withJson([
            'tickets' => $tickets,
        ]);
    }
}
