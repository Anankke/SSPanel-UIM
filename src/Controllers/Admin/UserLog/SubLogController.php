<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    UserSubscribeLog
};
use App\Utils\QQWry;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class SubLogController extends AdminController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        $id = $args['id'];
        $user = User::find($id);
        $table_config['total_column'] = array(
            'id'                  => 'ID',
            'subscribe_type'      => '类型',
            'request_ip'          => 'IP',
            'location'            => '归属地',
            'request_time'        => '时间',
            'request_user_agent'  => 'User-Agent'
        );
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
        $table_config['ajax_url'] = 'sublog/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('user', $user)
                ->display('admin/user/sublog.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax($request, $response, $args): ResponseInterface
    {
        $user  = User::find($args['id']);
        $query = UserSubscribeLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['location'])) {
                    $order_field = 'request_ip';
                }
            },
            static function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        );

        $data  = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var UserSubscribeLog $value */

            $tempdata                       = [];
            $tempdata['id']                 = $value->id;
            $tempdata['subscribe_type']     = $value->subscribe_type;
            $tempdata['request_ip']         = $value->request_ip;
            $tempdata['location']           = $value->location($QQWry);
            $tempdata['request_time']       = $value->request_time;
            $tempdata['request_user_agent'] = $value->request_user_agent;

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => UserSubscribeLog::where('user_id', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }
}
