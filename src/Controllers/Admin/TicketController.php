<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
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

class TicketController extends AdminController
{
    /**
     * 后台工单页面
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op'        => '操作',
            'id'        => 'ID',
            'datetime'  => '时间',
            'title'     => '标题',
            'userid'    => '用户ID',
            'user_name' => '用户名',
            'status'    => '状态'
        );
        $table_config['default_show_column'] = array(
            'op', 'id',
            'datetime', 'title', 'userid', 'user_name', 'status'
        );
        $table_config['ajax_url'] = 'ticket/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->display('admin/ticket/index.tpl')
        );
    }

    /**
     * 後臺創建新工單
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function add($request, $response, $args)
    {
        $title    = $request->getParam('title');
        $content  = $request->getParam('content');
        $markdown = $request->getParam('markdown');
        $userid   = $request->getParam('userid');
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
        $ticket->userid   = $userid;
        $ticket->datetime = time();
        $ticket->save();

        $user = User::find($userid);
        $user->sendMail(
            $_ENV['appName'] . '-新管理员工单被开启',
            'news/warn.tpl',
            [
                'text' => '管理员开启了新的工单，请您及时访问用户面板处理。'
            ],
            []
        );

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功'
        ]);
    }

    /**
     * 后台 更新工单内容
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function update($request, $response, $args)
    {
        $id      = $args['id'];
        $content = $request->getParam('content');
        $status  = $request->getParam('status');
        if ($content == '' || $status == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请填全'
            ]);
        }
        if (strpos($content, 'admin') !== false || strpos($content, 'user') !== false) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '请求中有不正当的词语。'
            ]);
        }
        $main = Ticket::find($id);
        $user = User::find($main->userid);
        $user->sendMail(
            $_ENV['appName'] . '-工单被回复',
            'news/warn.tpl',
            [
                'text' => '您好，有人回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $main->id . '/view">工单</a>，请您查看。'
            ],
            []
        );

        $antiXss                = new AntiXSS();
        $ticket                 = new Ticket();
        $ticket->title          = $antiXss->xss_clean($main->title);
        $ticket->content        = $antiXss->xss_clean($content);
        $ticket->rootid         = $main->id;
        $ticket->userid         = $this->user->id;
        $ticket->datetime       = time();
        $ticket->save();
        $main->status           = $status;
        $main->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '提交成功'
        ]);
    }

    /**
     * 后台 查看指定工单
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function show($request, $response, $args)
    {
        $id        = $args['id'];
        $pageNum   = $request->getQueryParams()['page'] ?? 1;
        $ticketset = Ticket::where('id', $id)->orWhere('rootid', '=', $id)->orderBy('datetime', 'desc')->paginate(5, ['*'], 'page', $pageNum);
        $ticketset->setPath('/admin/ticket/' . $id . '/view');

        $render = Tools::paginate_render($ticketset);
        return $response->write(
            $this->view()
                ->assign('ticketset', $ticketset)
                ->assign('id', $id)
                ->assign('render', $render)
                ->display('admin/ticket/view.tpl')
        );
    }

    /**
     * 后台工单页面 AJAX
     *
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax($request, $response, $args)
    {
        $query = Ticket::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['op'])) {
                    $order_field = 'id';
                }
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'userid';
                }
            },
            static function ($query) {
                $query->where('rootid', 0);
            }
        );

        $data  = [];
        foreach ($query['datas'] as $value) {
            /** @var Ticket $value */

            if ($value->user() == null) {
                Ticket::user_is_null($value);
                continue;
            }
            $tempdata               = [];
            $tempdata['op']         = '<a class="btn btn-brand" href="/admin/ticket/' . $value->id . '/view">查看</a>';
            $tempdata['id']         = $value->id;
            $tempdata['datetime']   = $value->datetime();
            $tempdata['title']      = $value->title;
            $tempdata['userid']     = $value->userid;
            $tempdata['user_name']  = $value->user_name();
            $tempdata['status']     = $value->status();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => Ticket::count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }
}
