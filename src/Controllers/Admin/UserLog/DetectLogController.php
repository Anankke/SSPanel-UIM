<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    Node,
    DetectLog,
    DetectRule
};
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

class DetectLogController extends AdminController
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
            'node_id'     => '节点ID',
            'node_name'   => '节点名',
            'rule_id'     => '规则ID',
            'rule_name'   => '规则名',
            'rule_text'   => '规则描述',
            'rule_regex'  => '规则正则表达式',
            'rule_type'   => '规则类型',
            'datetime'    => '时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'detect/ajax';

        return $response->write(
            $this->view()
                ->assign('table_config', $table_config)
                ->assign('user', $user)
                ->display('admin/user/detect.tpl')
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
        $datas        = DetectLog::where('user_id', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut  = DetectLog::where('user_id', $user->id)->count();
        $out_data     = [];
        foreach ($datas as $data) {
            $tempdata               = [];
            $tempdata['id']         = $data->id;
            $node                   = Node::where('id', $data->node_id)->first();
            $tempdata['node_id']    = $data->node_id;
            $tempdata['node_name']  = $node->name;
            $rule                   = DetectRule::where('id', $data->list_id)->first();
            $tempdata['rule_id']    = $rule->id;
            $tempdata['rule_name']  = $rule->name;
            $tempdata['rule_text']  = $rule->text;
            $tempdata['rule_regex'] = $rule->regex;
            $tempdata['rule_type']  = ($rule->type == 1 ? '数据包明文匹配' : '数据包十六进制匹配');
            $tempdata['datetime']   = date('Y-m-d H:i:s', $data->datetime);
            $out_data[]             = $tempdata;
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
