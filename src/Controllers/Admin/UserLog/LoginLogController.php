<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\BaseController;
use App\Models\LoginIp;
use App\Models\User;
use App\Utils\QQWry;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class LoginLogController extends BaseController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $user = User::find($id);
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'ip' => 'IP',
                    'location' => '归属地',
                    'datetime' => '时间',
                    'type' => '类型',
                ], 'login/ajax'))
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
