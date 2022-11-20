<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Ticket;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

/**
 *  TicketController
 */
final class TicketController extends BaseController
{
    /**
     * @param array     $args
     */
    public function ticket(Request $request, Response $response, array $args): ?ResponseInterface
    {
        if ($_ENV['enable_ticket'] !== true) {
            return null;
        }
        $tickets = Ticket::where('userid', $this->user->id)->orderBy('datetime', 'desc')->get();

        if ($request->getParam('json') === 1) {
            return $response->withJson([
                'ret' => 1,
                'tickets' => $tickets,
            ]);
        }

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->display('user/ticket/index.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ticketAdd(Request $request, Response $response, array $args): ResponseInterface
    {
        $title = $request->getParam('title');
        $comment = $request->getParam('comment');
        if ($title === '' || $comment === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }

        $antiXss = new AntiXSS();

        $content = [
            [
                'comment_id' => 0,
                'commenter_name' => $this->user->user_name,
                'comment' => $antiXss->xss_clean($comment),
                'datetime' => \time(),
            ],
        ];

        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($title);
        $ticket->content = \json_encode($content);
        $ticket->userid = $this->user->id;
        $ticket->datetime = \time();
        $ticket->status = 'open_wait_admin';
        $ticket->save();

        if ($_ENV['mail_ticket'] === true) {
            $adminUser = User::where('is_admin', 1)->get();
            foreach ($adminUser as $user) {
                $user->sendMail(
                    $_ENV['appName'] . '-新工单被开启',
                    'news/warn.tpl',
                    [
                        'text' => '管理员，有人开启了新的工单，请您及时处理。',
                    ],
                    []
                );
            }
        }
        if ($_ENV['useScFtqq'] === true) {
            $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
            $postdata = http_build_query([
                'text' => $_ENV['appName'] . '-新工单被开启',
                'desp' => $title,
            ]);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                ],
            ];
            $context = stream_context_create($opts);
            file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function ticketUpdate(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $content = $request->getParam('content');
        $status = $request->getParam('status');

        if ($content === '' || $status === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }

        $ticket = Ticket::where('id', $id)->where('userid', $this->user->id)->first();

        if ($ticket === null) {
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }

        $antiXss = new AntiXSS();

        $content_old = \json_decode($ticket->content, true);
        $content_new = [
            'comment_id' => $content_old[count($content_old) - 1]['comment_id'] + 1,
            'commenter_name' => $this->user->user_name,
            'comment' => $antiXss->xss_clean($content),
            'datetime' => \time(),
        ];

        $ticket = new Ticket();
        $ticket->content = \json_encode(\array_merge($content_old, $content_new));
        $ticket->status = 'open_wait_admin';
        $ticket->save();

        if ($_ENV['mail_ticket'] === true) {
            $adminUser = User::where('is_admin', 1)->get();
            foreach ($adminUser as $user) {
                $user->sendMail(
                    $_ENV['appName'] . '-工单被回复',
                    'news/warn.tpl',
                    [
                        'text' => '管理员，有人回复了<a href="' . $_ENV['baseUrl'] . '/admin/ticket/' . $ticket->id . '/view">工</a>，请您及时处理。',
                    ],
                    []
                );
            }
        }
        if ($_ENV['useScFtqq'] === true) {
            $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
            $postdata = http_build_query([
                'text' => $_ENV['appName'] . '-工单被回复',
                'desp' => $ticket->title,
            ]);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                ],
            ];
            $context = stream_context_create($opts);
            file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function ticketView(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $ticket = Ticket::where('id', '=', $id)->where('userid', $this->user->id)->first();
        $comments = \json_decode($ticket->content, true);

        if ($ticket === null) {
            if ($request->getParam('json') === 1) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '无访问权限',
                ]);
            }
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }
        if ($request->getParam('json') === 1) {
            return $response->withJson([
                'ret' => 1,
                'ticket' => $ticket,
            ]);
        }
        return $response->write(
            $this->view()
                ->assign('ticket', $ticket)
                ->assign('comments', $comments)
                ->display('user/ticket/view.tpl')
        );
    }
}
