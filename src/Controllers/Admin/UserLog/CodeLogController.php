<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use Code;
use Psr\Http\Message\ResponseInterface;
use Request;

class CodeLogController extends AdminController
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
            'code' => '内容',
            'type' => '类型',
            'number' => '操作',
            'usedatetime' => '时间',
        ];
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
        $table_config['ajax_url'] = 'code/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
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
