<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\BaseController;
use App\Models\Code;
use App\Models\User;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class CodeLogController extends BaseController
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
                    'code' => '内容',
                    'type' => '类型',
                    'number' => '操作',
                    'usedatetime' => '时间',
                ], 'code/ajax'))
                ->assign('user', $user)
                ->display('admin/user/code.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $user = User::find($args['id']);
        $query = Code::getTableDataFromAdmin(
            $request,
            null,
            static function ($query) use ($user): void {
                $query->where('userid', $user->id);
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var Code $value */

            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['code'] = $value->code;
            $tempdata['type'] = $value->type();
            $tempdata['number'] = $value->number();
            $tempdata['usedatetime'] = $value->usedatetime;

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => Code::where('userid', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
