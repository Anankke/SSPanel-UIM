<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Models\DetectRule;
use App\Utils\ResponseHelper;
use App\Utils\Telegram;
use Slim\Http\Request;
use Slim\Http\Response;

final class DetectController extends BaseController
{
    /**
     * @param array     $args
     */
    public function index(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'op' => '操作',
                    'id' => 'ID',
                    'name' => '名称',
                    'text' => '介绍',
                    'regex' => '正则表达式',
                    'type' => '类型',
                ], 'detect/ajax'))
                ->display('admin/detect/index.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajaxRule(Request $request, Response $response, array $args)
    {
        $query = DetectRule::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['op'])) {
                    $order_field = 'id';
                }
            }
        );

        $data = [];
        foreach ($query['datas'] as $value) {
            /** @var DetectRule $value */

            $tempdata = [];
            $tempdata['op'] = '<a class="btn btn-brand" href="/admin/detect/' . $value->id . '/edit">编辑</a> <a class="btn btn-brand-accent" id="delete" value="' . $value->id . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $value->id . '\')">删除</a>';
            $tempdata['id'] = $value->id;
            $tempdata['name'] = $value->name;
            $tempdata['text'] = $value->text;
            $tempdata['regex'] = $value->regex;
            $tempdata['type'] = $value->type();

            $data[] = $tempdata;
        }

        return $response->withJson([
            'draw' => $request->getParam('draw'),
            'recordsTotal' => DetectRule::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }

    /**
     * @param array     $args
     */
    public function create(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->display('admin/detect/add.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function add(Request $request, Response $response, array $args)
    {
        $rule = new DetectRule();
        $rule->name = $request->getParam('name');
        $rule->text = $request->getParam('text');
        $rule->regex = $request->getParam('regex');
        $rule->type = $request->getParam('type');

        if (! $rule->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '添加失败',
            ]);
        }

        Telegram::sendMarkdown('有新的审计规则：' . $rule->name);
        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function edit(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $rule = DetectRule::find($id);
        return $response->write(
            $this->view()
                ->assign('rule', $rule)
                ->display('admin/detect/edit.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function update(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $rule = DetectRule::find($id);

        $rule->name = $request->getParam('name');
        $rule->text = $request->getParam('text');
        $rule->regex = $request->getParam('regex');
        $rule->type = $request->getParam('type');

        if (! $rule->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }
        Telegram::sendMarkdown('规则更新：' . PHP_EOL . $request->getParam('name'));
        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $request->getParam('id');
        $rule = DetectRule::find($id);
        if (! $rule->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    /**
     * @param array     $args
     */
    public function log(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()
                ->assign('table_config', ResponseHelper::buildTableConfig([
                    'id' => 'ID',
                    'user_id' => '用户ID',
                    'user_name' => '用户名',
                    'node_id' => '节点ID',
                    'node_name' => '节点名',
                    'list_id' => '规则ID',
                    'rule_name' => '规则名',
                    'rule_text' => '规则描述',
                    'rule_regex' => '规则正则表达式',
                    'rule_type' => '规则类型',
                    'datetime' => '时间',
                ], 'log/ajax'))
                ->display('admin/detect/log.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function ajaxLog(Request $request, Response $response, array $args)
    {
        $query = DetectLog::getTableDataFromAdmin(
            $request,
            static function (&$order_field): void {
                if (in_array($order_field, ['node_name'])) {
                    $order_field = 'node_id';
                }
                if (in_array($order_field, ['rule_name', 'rule_text', 'rule_regex', 'rule_type'])) {
                    $order_field = 'list_id';
                }
                if (in_array($order_field, ['user_name'])) {
                    $order_field = 'user_id';
                }
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
            if ($value->user() === null) {
                DetectLog::userIsNull($value);
                continue;
            }
            $tempdata = [];
            $tempdata['id'] = $value->id;
            $tempdata['user_id'] = $value->user_id;
            $tempdata['user_name'] = $value->userName();
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
            'recordsTotal' => DetectLog::count(),
            'recordsFiltered' => $query['count'],
            'data' => $data,
        ]);
    }
}
