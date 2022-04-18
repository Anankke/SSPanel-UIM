<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Utils\QQWry;
use Psr\Http\Message\ResponseInterface;
use Request;
use User;

class LoginLogController extends AdminController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $user = User::find($id);
        $table_config['total_column'] = [
            'id' => 'ID',
            'ip' => 'IP',
            'location' => '归属地',
            'datetime' => '时间',
            'type' => '类型',
        ];
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
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $user = User::find($args['id']);
        $query = LoginIp::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['location'])) {
                    $order_field = 'ip';
                }
            },
            static function ($query) use ($user): void {
                $query->where('userid', $user->id);
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var LoginIp $value */
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['ip'] = $value->ip;
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['datetime'] = $value->datetime();
            $tempdata['type'] = $value->type();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => LoginIp::where('userid', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
