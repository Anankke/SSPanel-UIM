<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UserSubscribeLog;
use App\Utils\QQWry;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class SubLogController extends BaseController
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
                    'subscribe_type' => '类型',
                    'request_ip' => 'IP',
                    'location' => '归属地',
                    'request_time' => '时间',
                    'request_user_agent' => 'User-Agent',
                ], 'sublog/ajax'))
                ->assign('user', $user)
                ->display('admin/user/sublog.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $user = User::find($args['id']);
        $query = UserSubscribeLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['location'])) {
                    $order_field = 'request_ip';
                }
            },
            static function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            }
        );

        $data = [];
        $QQWry = new QQWry();
        foreach ($query['datas'] as $value) {
            /** @var UserSubscribeLog $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['subscribe_type'] = $value->subscribe_type;
            $tempdata['request_ip'] = $value->request_ip;
            $tempdata['location'] = $value->location($QQWry);
            $tempdata['request_time'] = $value->request_time;
            $tempdata['request_user_agent'] = $value->request_user_agent;

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => UserSubscribeLog::where('user_id', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
