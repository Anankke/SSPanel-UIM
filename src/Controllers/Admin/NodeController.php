<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\Node;
use App\Services\IM\Telegram;
use App\Utils\Tools;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Smarty\Exception as SmartyException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function json_decode;
use function json_encode;
use function round;
use function str_replace;
use function trim;

final class NodeController extends BaseController
{
    private static array $details = [
        'field' => [
            'op' => '操作',
            'id' => '节点ID',
            'name' => '名称',
            'server' => '地址',
            'type' => '状态',
            'sort' => '类型',
            'traffic_rate' => '倍率',
            'is_dynamic_rate' => '动态倍率',
            'dynamic_rate_type' => '动态倍率计算方式',
            'node_class' => '等级',
            'node_group' => '组别',
            'node_bandwidth_limit' => '流量限制/GB',
            'node_bandwidth' => '已用流量/GB',
            'bandwidthlimit_resetday' => '重置日',
        ],
    ];

    private static array $update_field = [
        'name',
        'server',
        'traffic_rate',
        'is_dynamic_rate',
        'dynamic_rate_type',
        'max_rate',
        'max_rate_time',
        'min_rate',
        'min_rate_time',
        'node_group',
        'node_speedlimit',
        'sort',
        'node_class',
        'node_bandwidth_limit',
        'bandwidthlimit_resetday',
    ];

    /**
     * 后台节点页面
     *
     * @throws SmartyException
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('details', self::$details)
                ->fetch('admin/node/index.tpl')
        );
    }

    /**
     * 后台创建节点页面
     *
     * @throws SmartyException
     */
    public function create(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->fetch('admin/node/create.tpl')
        );
    }

    /**
     * 后台添加节点
     */
    public function add(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = new Node();

        $node->name = $request->getParam('name');
        $node->node_group = $request->getParam('node_group');
        $node->server = trim($request->getParam('server'));
        $node->traffic_rate = $request->getParam('traffic_rate') ?? 1;
        $node->is_dynamic_rate = $request->getParam('is_dynamic_rate') === 'true' ? 1 : 0;
        $node->dynamic_rate_type = $request->getParam('dynamic_rate_type') ?? 0;
        $node->dynamic_rate_config = json_encode([
            'max_rate' => $request->getParam('max_rate') ?? 1,
            'max_rate_time' => $request->getParam('max_rate_time') ?? 22,
            'min_rate' => $request->getParam('min_rate') ?? 1,
            'min_rate_time' => $request->getParam('min_rate_time') ?? 3,
        ]);

        $custom_config = $request->getParam('custom_config') ?? '{}';

        if ($custom_config !== '') {
            $node->custom_config = $custom_config;
        } else {
            $node->custom_config = '{}';
        }

        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type') === 'true' ? 1 : 0;
        $node->sort = $request->getParam('sort');
        $node->node_class = $request->getParam('node_class');
        $node->node_bandwidth_limit = Tools::toGB($request->getParam('node_bandwidth_limit'));
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');
        $node->password = Tools::genRandomChar(32);

        if (! $node->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '添加失败',
            ]);
        }

        if (Config::obtain('telegram_add_node')) {
            try {
                (new Telegram())->send(
                    0,
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        Config::obtain('telegram_add_node_text')
                    )
                );
            } catch (TelegramSDKException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '添加成功，但 Telegram 通知失败',
                    'node_id' => $node->id,
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '添加成功',
            'node_id' => $node->id,
        ]);
    }

    /**
     * 后台编辑指定节点页面
     *
     * @throws SmartyException
     */
    public function edit(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = (new Node())->find($args['id']);

        $dynamic_rate_config = json_decode($node->dynamic_rate_config);
        $node->max_rate = $dynamic_rate_config?->max_rate ?? 1;
        $node->max_rate_time = $dynamic_rate_config?->max_rate_time ?? 22;
        $node->min_rate = $dynamic_rate_config?->min_rate ?? 1;
        $node->min_rate_time = $dynamic_rate_config?->min_rate_time ?? 3;

        $node->node_bandwidth = Tools::autoBytes($node->node_bandwidth);
        $node->node_bandwidth_limit = Tools::flowToGB($node->node_bandwidth_limit);

        return $response->write(
            $this->view()
                ->assign('node', $node)
                ->assign('update_field', self::$update_field)
                ->fetch('admin/node/edit.tpl')
        );
    }

    /**
     * 后台更新指定节点内容
     */
    public function update(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = (new Node())->find($args['id']);

        $node->name = $request->getParam('name');
        $node->node_group = $request->getParam('node_group') ?? 0;
        $node->server = trim($request->getParam('server'));
        $node->traffic_rate = $request->getParam('traffic_rate') ?? 1;
        $node->is_dynamic_rate = $request->getParam('is_dynamic_rate') === 'true' ? 1 : 0;
        $node->dynamic_rate_type = $request->getParam('dynamic_rate_type') ?? 0;
        $node->dynamic_rate_config = json_encode([
            'max_rate' => $request->getParam('max_rate') ?? 1,
            'max_rate_time' => $request->getParam('max_rate_time') ?? 0,
            'min_rate' => $request->getParam('min_rate') ?? 1,
            'min_rate_time' => $request->getParam('min_rate_time') ?? 0,
        ]);

        $custom_config = $request->getParam('custom_config') ?? '{}';

        if ($custom_config !== '') {
            $node->custom_config = $custom_config;
        } else {
            $node->custom_config = '{}';
        }

        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type') === 'true' ? 1 : 0;
        $node->sort = $request->getParam('sort');
        $node->node_class = $request->getParam('node_class');
        $node->node_bandwidth_limit = Tools::toGB($request->getParam('node_bandwidth_limit'));
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        if (! $node->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }

        if (Config::obtain('telegram_update_node')) {
            try {
                (new Telegram())->send(
                    0,
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        Config::obtain('telegram_update_node_text')
                    )
                );
            } catch (TelegramSDKException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '修改成功，但 Telegram 通知失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    public function resetPassword(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = (new Node())->find($args['id']);
        $node->password = Tools::genRandomChar(32);
        $node->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置节点通讯密钥成功',
        ]);
    }

    public function resetBandwidth(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = (new Node())->find($args['id']);
        $node->node_bandwidth = 0;
        $node->save();

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置节点流量成功',
        ]);
    }

    /**
     * 后台删除指定节点
     */
    public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node = (new Node())->find($args['id']);

        if (! $node->delete()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '删除失败',
            ]);
        }

        if (Config::obtain('telegram_delete_node')) {
            try {
                (new Telegram())->send(
                    0,
                    str_replace(
                        '%node_name%',
                        $node->name,
                        Config::obtain('telegram_delete_node_text')
                    )
                );
            } catch (TelegramSDKException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '删除成功，但Telegram通知失败',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '删除成功',
        ]);
    }

    public function copy($request, $response, $args)
    {
        $old_node = (new Node())->find($args['id']);
        $new_node = $old_node->replicate([
            'node_bandwidth',
        ]);
        $new_node->name .= ' (副本)';
        $new_node->node_bandwidth = 0;
        $new_node->password = Tools::genRandomChar(32);

        if (! $new_node->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '复制失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '复制成功',
        ]);
    }

    /**
     * 后台节点页面 AJAX
     */
    public function ajax(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $nodes = (new Node())->orderBy('id', 'desc')->get();

        foreach ($nodes as $node) {
            $node->op = '<button type="button" class="btn btn-red" id="delete-node-' . $node->id . '" 
            onclick="deleteNode(' . $node->id . ')">删除</button>
            <button type="button" class="btn btn-orange" id="copy-node-' . $node->id . '" 
            onclick="copyNode(' . $node->id . ')">复制</button>
            <a class="btn btn-blue" href="/admin/node/' . $node->id . '/edit">编辑</a>';
            $node->type = $node->type();
            $node->sort = $node->sort();
            $node->is_dynamic_rate = $node->isDynamicRate();
            $node->node_bandwidth = round(Tools::flowToGB($node->node_bandwidth), 2);
            $node->node_bandwidth_limit = Tools::flowToGB($node->node_bandwidth_limit);
        }

        return $response->withJson([
            'nodes' => $nodes,
        ]);
    }
}
