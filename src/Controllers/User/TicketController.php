<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Ticket;
use App\Models\User;
use App\Utils\Tools;
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
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $tickets = Ticket::where('userid', $this->user->id)->where('rootid', 0)->orderBy('datetime', 'desc')->paginate(15, ['*'], 'page', $pageNum);

        if ($request->getParam('json') === 1) {
            return $response->withJson([
                'ret' => 1,
                'tickets' => $tickets,
            ]);
        }
        $render = Tools::paginateRender($tickets);

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->assign('render', $render)
                ->display('user/ticket.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ticketCreate(Request $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->display('user/ticket_create.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ticketAdd(Request $request, Response $response, array $args): ResponseInterface
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $markdown = $request->getParam('markdown');
        if ($title === '' || $content === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }

        $ticket = new Ticket();
        $antiXss = new AntiXSS();
        $ticket->title = $antiXss->xss_clean($title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = 0;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket->save();

        if ($_ENV['mail_ticket'] === true && $markdown !== '') {
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
        if ($_ENV['useScFtqq'] === true && $markdown !== '') {
            $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
            $postdata = http_build_query([
                'text' => $_ENV['appName'] . '-新工单被开启',
                'desp' => $markdown,
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
        $markdown = $request->getParam('markdown');
        if ($content === '' || $status === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }
        $ticket_main = Ticket::where('id', $id)->where('userid', $this->user->id)->where('rootid', 0)->first();
        if ($ticket_main === null) {
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }
        if ($status === 1 && $ticket_main->status !== $status) {
            if ($_ENV['mail_ticket'] === true && $markdown !== '') {
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($adminUser as $user) {
                    $user->sendMail(
                        $_ENV['appName'] . '-工单被重新开启',
                        'news/warn.tpl',
                        [
                            'text' => '管理员，有人重新开启了<a href="' . $_ENV['baseUrl'] . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。',
                        ],
                        []
                    );
                }
            }
            if ($_ENV['useScFtqq'] === true && $markdown !== '') {
                $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                $postdata = http_build_query([
                    'text' => $_ENV['appName'] . '-工单被重新开启',
                    'desp' => $markdown,
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
        } else {
            if ($_ENV['mail_ticket'] === true && $markdown !== '') {
                $adminUser = User::where('is_admin', 1)->get();
                foreach ($adminUser as $user) {
                    $user->sendMail(
                        $_ENV['appName'] . '-工单被回复',
                        'news/warn.tpl',
                        [
                            'text' => '管理员，有人回复了<a href="' . $_ENV['baseUrl'] . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。',
                        ],
                        []
                    );
                }
            }
            if ($_ENV['useScFtqq'] === true && $markdown !== '') {
                $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                $postdata = http_build_query([
                    'text' => $_ENV['appName'] . '-工单被回复',
                    'desp' => $markdown,
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
        }

        $antiXss = new AntiXSS();
        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($ticket_main->title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = $ticket_main->id;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket_main->status = $status;

        $ticket_main->save();
        $ticket->save();

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
        $ticket_main = Ticket::where('id', '=', $id)->where('userid', $this->user->id)->where('rootid', '=', 0)->first();
        if ($ticket_main === null) {
            if ($request->getParam('json') === 1) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '这不是你的工单！',
                ]);
            }
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $ticketset = Ticket::where('id', $id)->orWhere('rootid', '=', $id)->orderBy('datetime', 'desc')->paginate(5, ['*'], 'page', $pageNum);
        if ($request->getParam('json') === 1) {
            foreach ($ticketset as $set) {
                $set->username = $set->user()->user_name;
                $set->datetime = $set->datetime();
            }
            return $response->withJson([
                'ret' => 1,
                'tickets' => $ticketset,
            ]);
        }
        $render = Tools::paginateRender($ticketset);
        return $response->write(
            $this->view()
                ->assign('ticketset', $ticketset)
                ->assign('id', $id)
                ->assign('render', $render)
                ->display('user/ticket_view.tpl')
        );
    }
}
