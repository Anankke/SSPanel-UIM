<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\{
    User,
    Ticket
};
use App\Services\Auth;
use App\Utils\DatatablesHelper;
use voku\helper\AntiXSS;
use Ozdemir\Datatables\Datatables;

class TicketController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op' => '操作', 'id' => 'ID',
            'datetime' => '时间', 'title' => '标题', 'userid' => '用户ID',
            'user_name' => '用户名', 'status' => '状态'
        );
        $table_config['default_show_column'] = array(
            'op', 'id',
            'datetime', 'title', 'userid', 'user_name', 'status'
        );
        $table_config['ajax_url'] = 'ticket/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/ticket/index.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $content = $request->getParam('content');
        $status = $request->getParam('status');

        if ($content == '' || $status == '') {
            $res['ret'] = 0;
            $res['msg'] = '请填全';
            return $this->echoJson($response, $res);
        }

        if (strpos($content, 'admin') != false || strpos($content, 'user') != false) {
            $res['ret'] = 0;
            $res['msg'] = '请求中有不正当的词语。';
            return $this->echoJson($response, $res);
        }

        $ticket_main = Ticket::where('id', '=', $id)->where('rootid', '=', 0)->first();

        //if($status==1&&$ticket_main->status!=$status)
        {
            $adminUser = User::where('id', '=', $ticket_main->userid)->get();
            foreach ($adminUser as $user) {
                $user->sendMail(
                    $_ENV['appName'] . '-工单被回复',
                    'news/warn.tpl',
                    [
                        'text' => '您好，有人回复了<a href="' . $_ENV['baseUrl'] . '/user/ticket/' . $ticket_main->id . '/view">工单</a>，请您查看。'
                    ],
                    []
                );
            }
        }

        $antiXss = new AntiXSS();

        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($ticket_main->title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = $ticket_main->id;
        $ticket->userid = Auth::getUser()->id;
        $ticket->datetime = time();
        $ticket_main->status = $status;

        $ticket_main->save();
        $ticket->save();

        $res['ret'] = 1;
        $res['msg'] = '提交成功';
        return $this->echoJson($response, $res);
    }

    public function show($request, $response, $args)
    {
        $id = $args['id'];

        $pageNum = $request->getQueryParams()['page'] ?? 1;


        $ticketset = Ticket::where('id', $id)->orWhere('rootid', '=', $id)->orderBy('datetime', 'desc')->paginate(5, ['*'], 'page', $pageNum);
        $ticketset->setPath('/admin/ticket/' . $id . '/view');

        return $this->view()->assign('ticketset', $ticketset)->assign('id', $id)->display('admin/ticket/view.tpl');
    }

    public function ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select ticket.id as op,ticket.id,ticket.datetime,ticket.title,ticket.userid,user.user_name,ticket.status from ticket,user where ticket.userid = user.id and ticket.rootid = 0');

        $datatables->edit('op', static function ($data) {
            return '<a class="btn btn-brand" href="/admin/ticket/' . $data['id'] . '/view">查看</a>';
        });

        $datatables->edit('datetime', static function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $datatables->edit('status', static function ($data) {
            return $data['status'] == 1 ? '开启' : '关闭';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
