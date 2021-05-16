<?php

namespace App\Controllers\User;

use App\Controllers\UserController;
use App\Models\{
    User,
    Ticket
};
use App\Utils\Tools;
use voku\helper\AntiXSS;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

/**
 *  TicketController
 */
class TicketController extends UserController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ticket($request, $response, $args): ?ResponseInterface
    {
        if ($_ENV['enable_ticket'] != true) {
            return null;
        }
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $tickets = Ticket::where('userid', $this->user->id)->where('rootid', 0)->orderBy('datetime', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $tickets->setPath('/user/ticket');

        if ($request->getParam('json') == 1) {
            return $response->withJson([
                'ret'     => 1,
                'tickets' => $tickets
            ]);
        }
        $render = Tools::paginate_render($tickets);

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->assign('render', $render)
                ->display('user/ticket.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ticket_create($request, $response, $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->display('user/ticket_create.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ticket_add($request, $response, $args): ResponseInterface
    {
        $title    = $request->getParam('title');
        $content  = $request->getParam('content');
        $markdown = $request->getParam('markdown');
        if ($title == '' || $content == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入'
            ]);
        }
        if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求中有不当词语'
            ]);
        }

        $ticket           = new Ticket();
        $antiXss          = new AntiXSS();
        $ticket->title    = $antiXss->xss_clean($title);
        $ticket->content  = $antiXss->xss_clean($content);
        $ticket->rootid   = 0;
        $ticket->userid   = $this->user->id;
        $ticket->datetime = time();
        $ticket->save();

        if ($_ENV['mail_ticket'] == true && $markdown != '') {
            $adminUser = User::where('is_admin', 1)->get();
            foreach ($adminUser as $user) {
                $user->sendMail(
                    $_ENV['appName'] . '-新工单被开启',
                    'news/warn.tpl',
                    [
                        'text' => '管理员，有人开启了新的工单，请您及时处理。'
                    ],
                    []
                );
            }
        }
        if ($_ENV['useScFtqq'] == true && $markdown != '') {
            $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
            $postdata = http_build_query([
                'text' => $_ENV['appName'] . '-新工单被开启',
                'desp' => $markdown
            ]);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                ]
            ];
            $context = stream_context_create($opts);
            file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功'
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ticket_update($request, $response, $args): ResponseInterface
    {
        $id       = $args['id'];
        $content  = $request->getParam('content');
        $status   = $request->getParam('status');
        $markdown = $request->getParam('markdown');
        if ($content == '' || $status == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入'
            ]);
        }
        if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求中有不当词语'
            ]);
        }
        $ticket_main = Ticket::where('id', $id)->where('userid', $this->user->id)->where('rootid', 0)->first();
        if ($ticket_main == null) {
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');;
        }
        if ($status == 1 && $ticket_main->status != $status) {
            if ($_ENV['mail_ticket'] == true && $markdown != '') {
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($adminUser as $user) {
                    $user->sendMail(
                        $_ENV['appName'] . '-工单被重新开启',
                        'news/warn.tpl',
                        [
                            'text' => '管理员，有人重新开启了<a href="' . $_ENV['baseUrl'] . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。'
                        ],
                        []
                    );
                }
            }
            if ($_ENV['useScFtqq'] == true && $markdown != '') {
                $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                $postdata = http_build_query([
                    'text' => $_ENV['appName'] . '-工单被重新开启',
                    'desp' => $markdown
                ]);
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    ]
                ];
                $context = stream_context_create($opts);
                file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
            }
        } else {
            if ($_ENV['mail_ticket'] == true && $markdown != '') {
                $adminUser = User::where('is_admin', 1)->get();
                foreach ($adminUser as $user) {
                    $user->sendMail(
                        $_ENV['appName'] . '-工单被回复',
                        'news/warn.tpl',
                        [
                            'text' => '管理员，有人回复了<a href="' . $_ENV['baseUrl'] . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。'
                        ],
                        []
                    );
                }
            }
            if ($_ENV['useScFtqq'] == true && $markdown != '') {
                $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                $postdata = http_build_query([
                    'text' => $_ENV['appName'] . '-工单被回复',
                    'desp' => $markdown
                ]);
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    ]
                ];
                $context = stream_context_create($opts);
                file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
            }
        }

        $antiXss              = new AntiXSS();
        $ticket               = new Ticket();
        $ticket->title        = $antiXss->xss_clean($ticket_main->title);
        $ticket->content      = $antiXss->xss_clean($content);
        $ticket->rootid       = $ticket_main->id;
        $ticket->userid       = $this->user->id;
        $ticket->datetime     = time();
        $ticket_main->status  = $status;

        $ticket_main->save();
        $ticket->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功'
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ticket_view($request, $response, $args): ResponseInterface
    {
        $id           = $args['id'];
        $ticket_main  = Ticket::where('id', '=', $id)->where('userid', $this->user->id)->where('rootid', '=', 0)->first();
        if ($ticket_main == null) {
            if ($request->getParam('json') == 1) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '这不是你的工单！'
                ]);
            }
            return $response->withStatus(302)->withHeader('Location', '/user/ticket');
        }
        $pageNum   = $request->getQueryParams()['page'] ?? 1;
        $ticketset = Ticket::where('id', $id)->orWhere('rootid', '=', $id)->orderBy('datetime', 'desc')->paginate(5, ['*'], 'page', $pageNum);
        $ticketset->setPath('/user/ticket/' . $id . '/view');
        if ($request->getParam('json') == 1) {
            foreach ($ticketset as $set) {
                $set->username = $set->user()->user_name;
                $set->datetime = $set->datetime();
            }
            return $response->withJson([
                'ret'     => 1,
                'tickets' => $ticketset
            ]);
        }
        $render = Tools::paginate_render($ticketset);
        return $response->write(
            $this->view()
                ->assign('ticketset', $ticketset)
                ->assign('id', $id)
                ->assign('render', $render)
                ->display('user/ticket_view.tpl')
        );
    }
}
