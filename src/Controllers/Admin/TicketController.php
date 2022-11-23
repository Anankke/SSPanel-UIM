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
    public $details =
    [
        'field' => [
            'id' => '#',
            'title' => '主题',
            'status' => '工单状态',
            'type' => '工单类型',
            'userid' => '提交用户',
            'datetime' => '创建时间',
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

        return $response->write(
            $this->view()
                ->assign('tickets', $tickets)
                ->assign('details', self::$details)
                ->display('admin/ticket/index.tpl')
        );
    }

    /**
     * 後臺創建新工單
     *
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args)
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $userid = $request->getParam('userid');
        if ($title === '' || $content === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法输入',
            ]);
        }
        if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求中有不当词语',
            ]);
        }

        $ticket = new Ticket();
        $antiXss = new AntiXSS();
        $ticket->title = $antiXss->xss_clean($title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->userid = $userid;
        $ticket->datetime = \time();
        $ticket->save();

        $user = User::find($userid);
        $user->sendMail(
            $_ENV['appName'] . '-新管理员工单被开启',
            'news/warn.tpl',
            [
                'text' => '管理员开启了新的工单，请您及时访问用户面板处理。',
            ],
            []
        );

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功',
        ]);
    }

    /**
     * 后台 更新工单内容
     *
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $content = $request->getParam('content');
        $status = $request->getParam('status');
        if ($content === '' || $status === '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填全',
            ]);
        }
        if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求中有不正当的词语。',
            ]);
        }
        $main = Ticket::find($id);
        $user = User::find($main->userid);
        $user->sendMail(
            $_ENV['appName'] . '-工单被回复',
            'news/warn.tpl',
            [
                'text' => '您好，有人回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $main->id . '/view">工单</a>，请您查看。',
            ],
            []
        );

        $antiXss = new AntiXSS();
        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($main->title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->userid = $this->user->id;
        $ticket->datetime = \time();
        $ticket->save();
        $main->status = $status;
        $main->save();

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
     * 后台工单页面 AJAX
     *
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args)
    {
        $condition = [];
        $details = self::$details;
        foreach ($details['search_dialog'] as $from) {
            $field = $from['id'];
            $keyword = $request->getParam($field);
            if ($from['type'] === 'input') {
                if ($from['exact']) {
                    ($keyword !== '') && array_push($condition, [$field, '=', $keyword]);
                } else {
                    ($keyword !== '') && array_push($condition, [$field, 'like', '%'.$keyword.'%']);
                }
            }
            if ($from['type'] === 'select') {
                ($keyword !== 'all') && array_push($condition, [$field, '=', $keyword]);
            }
        }

        $results = Ticket::orderBy('id', 'desc')
            ->where('is_topic', '1')
            ->where($condition)
            ->limit($_ENV['page_load_data_entry'])
            ->get();

        return $response->withJson([
            'ret' => 1,
            'result' => $results,
        ]);
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
