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
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
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
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $id           = $args['id'];
        $user         = User::find($id);
        $datas        = LoginIp::where('userid', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = LoginIp::where('userid', $user->id)->count();
        $iplocation   = new QQWry();
        $out_data     = [];
        foreach ($datas as $data) {
            $tempdata             = [];
            $tempdata['id']       = $data->id;
            $tempdata['ip']       = $data->ip;
            $location             = $iplocation->getlocation($data->ip);
            $tempdata['location'] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            $tempdata['datetime'] = date('Y-m-d H:i:s', $data->datetime);
            $tempdata['type']     = ($data->type == 0 ? '成功' : '失败');
            $out_data[]           = $tempdata;
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
