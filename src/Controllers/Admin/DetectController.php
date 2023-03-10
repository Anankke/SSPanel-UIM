<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectBanLog;
use App\Models\DetectLog;
use App\Models\DetectRule;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class DetectController extends BaseController
{
    public static array $rule_details =
    [
        'field' => [
            'op' => '操作',
            'id' => '规则ID',
            'name' => '规则名称',
            'text' => '规则介绍',
            'regex' => '正则表达式',
            'type' => '规则类型',
        ],
        'add_dialog' => [
            [
                'id' => 'name',
                'info' => '规则名称',
                'type' => 'input',
                'placeholder' => '审计规则名称',
            ],
            [
                'id' => 'text',
                'info' => '规则介绍',
                'type' => 'input',
                'placeholder' => '简洁明了地描述审计规则',
            ],
            [
                'id' => 'regex',
                'info' => '正则表达式',
                'type' => 'input',
                'placeholder' => '用以匹配审计内容的正则表达式',
            ],
            [
                'id' => 'type',
                'info' => '规则类型',
                'type' => 'select',
                'select' => [
                    '1' => '数据包明文匹配',
                    '0' => '数据包十六进制匹配',
                ],
            ],
        ],
    ];

    public static array $log_details =
    [
        'field' => [
            'id' => '事件ID',
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
        ],
    ];

    public static array $ban_details =
    [
        'field' => [
            'id' => '事件ID',
            'user_name' => '用户名',
            'user_id' => '用户ID',
            'email' => '用户邮箱',
            'detect_number' => '违规次数',
            'ban_time' => '封禁时长(分钟)',
            'start_time' => '统计开始时间',
            'end_time' => '统计结束&封禁开始时间',
            'ban_end_time' => '封禁结束时间',
            'all_detect_number' => '累计违规次数',
        ],
    ];

    /**
     * @throws Exception
     */
    public function detect(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$rule_details)
                ->fetch('admin/detect.tpl')
        );
    }

    /**
     * @throws TelegramSDKException
     */
    public function add(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
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

    public function delete(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $id = $args['id'];
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
     * @throws Exception
     */
    public function log(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$log_details)
                ->fetch('admin/log/detect.tpl')
        );
    }

    /**
     * @throws Exception
     */
    public function ban(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$ban_details)
                ->fetch('admin/log/detect_ban.tpl')
        );
    }

    public function ajaxRule(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $rules = DetectRule::orderBy('id', 'desc')->get();

        foreach ($rules as $rule) {
            $rule->op = '<button type="button" class="btn btn-red" id="delete-rule-' . $rule->id . '" 
            onclick="deleteRule(' . $rule->id . ')">删除</button>';
            $rule->type = $rule->type();
        }

        return $response->withJson([
            'rules' => $rules,
        ]);
    }

    public function ajaxLog(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $logs = DetectLog::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = DetectLog::count();

        foreach ($logs as $log) {
            $log->user_name = $log->userName();
            $log->node_name = $log->nodeName();
            $log->rule_name = $log->ruleName();
            $log->rule_text = $log->ruleText();
            $log->rule_regex = $log->ruleRegex();
            $log->rule_type = $log->ruleType();
            $log->datetime = Tools::toDateTime((int) $log->datetime);
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'logs' => $logs,
        ]);
    }

    public function ajaxBan(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $bans = DetectBanLog::orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = DetectBanLog::count();

        foreach ($bans as $ban) {
            $ban->start_time = Tools::toDateTime((int) $ban->start_time);
            $ban->end_time = Tools::toDateTime((int) $ban->end_time);
            $ban->ban_end_time = $ban->banEndTime();
        }

        return $response->withJson([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'bans' => $bans,
        ]);
    }
}
