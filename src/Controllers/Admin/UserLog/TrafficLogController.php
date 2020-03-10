<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    Node,
    TrafficLog
};
use App\Utils\Tools;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class TrafficLogController extends AdminController
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
            'id'              => 'ID',
            'node_name'       => '使用节点',
            'rate'            => '倍率',
            'origin_traffic'  => '实际使用流量',
            'traffic'         => '结算流量',
            'log_time'        => '记录时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'traffic/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('user', $user)
                ->display('admin/user/traffic.tpl')
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
        $datas        = TrafficLog::where('user_id', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = TrafficLog::where('user_id', $user->id)->count();
        $out_data     = [];
        foreach ($datas as $data) {
            $tempdata                   = [];
            $tempdata['id']             = $data->id;
            $node                       = Node::where('id', $data->node_id)->first();
            $tempdata['node_name']      = $node->name;
            $tempdata['rate']           = $data->rate;
            $tempdata['origin_traffic'] = Tools::flowAutoShow($data->u + $data->d);
            $tempdata['traffic']        = $data->traffic;
            $tempdata['log_time']       = date('Y-m-d H:i:s', $data->log_time);
            $out_data[]                 = $tempdata;
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
