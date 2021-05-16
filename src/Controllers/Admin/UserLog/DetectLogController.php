<?php

namespace App\Controllers\Admin\UserLog;

use App\Controllers\AdminController;
use App\Models\{
    User,
    DetectLog
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
            'list_id'     => '规则ID',
            'rule_name'   => '规则名',
            'rule_text'   => '规则描述',
            'rule_regex'  => '规则正则表达式',
            'rule_type'   => '规则类型',
            'datetime'    => '时间'
        );
        $table_config['default_show_column'] = array_keys($table_config['total_column']);
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
        $user  = User::find($args['id']);
        $query = DetectLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field) {
                if (in_array($order_field, ['node_name'])) {
                    $order_field = 'node_id';
                }
                if (in_array($order_field, ['rule_name', 'rule_text', 'rule_regex', 'rule_type'])) {
                    $order_field = 'list_id';
                }
            },
            static function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        );

        $data  = [];
        foreach ($query['datas'] as $value) {
            /** @var DetectLog $value */

            if ($value->rule() == null) {
                DetectLog::rule_is_null($value);
                continue;
            }
            if ($value->node() == null) {
                DetectLog::node_is_null($value);
                continue;
            }
            $tempdata               = [];
            $tempdata['id']         = $value->id;
            $tempdata['node_id']    = $value->node_id;
            $tempdata['node_name']  = $value->node_name();
            $tempdata['list_id']    = $value->list_id;
            $tempdata['rule_name']  = $value->rule_name();
            $tempdata['rule_text']  = $value->rule_text();
            $tempdata['rule_regex'] = $value->rule_regex();
            $tempdata['rule_type']  = $value->rule_type();
            $tempdata['datetime']   = $value->datetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw'            => $request->getParam('draw'),
            'recordsTotal'    => DetectLog::where('user_id', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data'            => $data,
        ]);
    }
}
