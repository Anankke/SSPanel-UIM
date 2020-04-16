<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    Code,
    User,
};
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class CodeLogController extends AdminController
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
            'id'          => 'ID',
            'code'        => '内容',
            'type'        => '类型',
            'number'      => '操作',
            'usedatetime' => '时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'code/ajax';
        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('user', $user)
                ->display('admin/user/code.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function ajax($request, $response, $args): ResponseInterface
    {
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $id           = $args['id'];
        $user         = User::find($id);
        $datas        = Code::where('userid', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = Code::where('userid', $user->id)->count();
        $out_data         = [];
        foreach ($datas as $data) {
            $tempdata                = [];
            $tempdata['id']          = $data->id;
            $tempdata['code']        = $data->code;
            switch ($data->type) {
                case -1:
                    $type = '充值金额';
                    break;
                case -2:
                    $type = '财务支出';
                    break;
                default:
                    $type = '已经废弃';
                    break;
            }
            $tempdata['type']        = $type;
            $tempdata['number']      = $data->number;
            $tempdata['usedatetime'] = $data->usedatetime;
            $out_data[]              = $tempdata;
        }
        $info = [
            'draw'              => $request->getParam('draw'),
            'recordsTotal'      => $total_conut,
            'recordsFiltered'   => $total_conut,
            'data'              => $out_data
        ];

        return $response->write(
            json_encode($info)
        );
    }
}
