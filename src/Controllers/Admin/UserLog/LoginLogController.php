<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    LoginIp
};
use App\Utils\QQWry;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class LoginLogController extends AdminController
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
            'id'        => 'ID',
            'ip'        => 'IP',
            'location'  => '归属地',
            'datetime'  => '时间',
            'type'      => '类型'
        );
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
        $table_config['ajax_url'] = 'login/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('user', $user)
                ->display('admin/user/login.tpl')
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
        $query = LoginIp::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            },
            static function ($query) use ($user) {
                $query->where('userid', $user->id);
            }
        );

        $data  = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var LoginIp $value */
            $tempdata             = [];
            $tempdata['id']        = $value->id;
            $tempdata['ip']        = $value->ip;
            $tempdata['location']  = $value->location($QQWry);
            $tempdata['datetime']  = $value->datetime();
            $tempdata['type']      = $value->type();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => LoginIp::where('userid', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }
}
