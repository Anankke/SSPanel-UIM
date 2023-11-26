<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectLog;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final class DetectLogController extends BaseController
{
    private static array $details =
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

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/log/detect.tpl')
        );
    }

    public function ajax(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $length = $request->getParam('length');
        $page = $request->getParam('start') / $length + 1;
        $draw = $request->getParam('draw');

        $logs = (new DetectLog())->orderBy('id', 'desc')->paginate($length, '*', '', $page);
        $total = (new DetectLog())->count();

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
}
