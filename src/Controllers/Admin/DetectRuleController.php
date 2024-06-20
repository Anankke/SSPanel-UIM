<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DetectRule;
use App\Services\IM\Telegram;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class DetectRuleController extends BaseController
{
    private static array $details =
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

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/detect.tpl')
        );
    }

    /**
     * @throws TelegramSDKException
     */
    public function add(ServerRequest $request, Response $response, array $args): ResponseInterface
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

        (new Telegram())->sendMarkdown(0, '有新的审计规则：' . $rule->name);
        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功',
        ]);
    }

    public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $rule = (new DetectRule())->find($id);
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

    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $rules = (new DetectRule())->orderBy('id', 'desc')->get();

        foreach ($rules as $rule) {
            $rule->op = '<button type="button" class="btn btn-red" id="delete-rule-' . $rule->id .
                '" onclick="deleteRule(' . $rule->id . ')">删除</button>';
            $rule->type = $rule->type();
        }

        return $response->withJson([
            'rules' => $rules,
        ]);
    }
}
