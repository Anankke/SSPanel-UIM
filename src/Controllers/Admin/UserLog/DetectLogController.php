<?php

declare(strict_types=1);

namespace App\Controllers\Admin\UserLog;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\User;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class DetectLogController extends BaseController
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
                    'node_id' => '节点ID',
                    'node_name' => '节点名',
                    'list_id' => '规则ID',
                    'rule_name' => '规则名',
                    'rule_text' => '规则描述',
                    'rule_regex' => '规则正则表达式',
                    'rule_type' => '规则类型',
                    'datetime' => '时间',
                ], 'detect/ajax'))
                ->assign('user', $user)
                ->display('admin/user/detect.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajax(Request $request, Response $response, array $args): ResponseInterface
    {
        $user = User::find($args['id']);
        $query = DetectLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['node_name'])) {
                    $order_field = 'node_id';
                }
                if (in_array($order_field, ['rule_name', 'rule_text', 'rule_regex', 'rule_type'])) {
                    $order_field = 'list_id';
                }
            },
            static function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var DetectLog $value */

            if ($value->rule() === null) {
                DetectLog::ruleIsNull($value);
                continue;
            }
            if ($value->node() === null) {
                DetectLog::nodeIsNull($value);
                continue;
            }
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['node_id'] = $value->node_id;
            $tempdata['node_name'] = $value->nodeName();
            $tempdata['list_id'] = $value->list_id;
            $tempdata['rule_name'] = $value->ruleName();
            $tempdata['rule_text'] = $value->ruleText();
            $tempdata['rule_regex'] = $value->ruleRegex();
            $tempdata['rule_type'] = $value->ruleType();
            $tempdata['datetime'] = $value->datetime();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => DetectLog::where('user_id', $user->id)->count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
