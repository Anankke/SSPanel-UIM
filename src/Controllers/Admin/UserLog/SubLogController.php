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
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
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
        $start        = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $id           = $args['id'];
        $user         = User::find($id);
        $datas        = UserSubscribeLog::where('user_id', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = UserSubscribeLog::where('user_id', $user->id)->count();
        $iplocation   = new QQWry();
        $out_data     = [];
        foreach ($datas as $data) {
            $tempdata                       = [];
            $tempdata['id']                 = $data->id;
            $tempdata['subscribe_type']     = $data->subscribe_type;
            $tempdata['request_ip']         = $data->request_ip;
            $location                       = $iplocation->getlocation($data->request_ip);
            $tempdata['location']           = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            $tempdata['request_time']       = $data->request_time;
            $tempdata['request_user_agent'] = $data->request_user_agent;
            $out_data[]                     = $tempdata;
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
