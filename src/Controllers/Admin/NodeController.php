<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\Node;
use App\Services\I18n;
use App\Services\Notification;
use App\Utils\Tools;
use GuzzleHttp\Exception\GuzzleException;
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
        $error = $this->validateCustomConfig((int) $request->getParam('sort'), $custom_config);
        if ($error) {
            return $response->withJson(['ret' => 0, 'msg' => $error]);
        }

        if ($custom_config !== '') {
            $node->custom_config = $custom_config;
        } else {
            $node->custom_config = '{}';
        }

        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type') === 'true' ? 1 : 0;
        $node->sort = $request->getParam('sort');
        $node->node_class = $request->getParam('node_class');
        $node->node_bandwidth_limit = Tools::gbToB($request->getParam('node_bandwidth_limit'));
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');
        $node->password = Tools::genRandomChar(32);

        if (! $node->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '添加失败',
            ]);
        }

        if (Config::obtain('im_bot_group_notify_add_node')) {
            try {
                Notification::notifyUserGroup(
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        I18n::trans('bot.node_added', $_ENV['locale'])
                    )
                );
            } catch (TelegramSDKException | GuzzleException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '添加成功，但 IM Bot 通知失败',
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
        $node->node_bandwidth_limit = Tools::bToGB($node->node_bandwidth_limit);

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
        $error = $this->validateCustomConfig((int) $request->getParam('sort'), $custom_config);
        if ($error) {
            return $response->withJson(['ret' => 0, 'msg' => $error]);
        }

        if ($custom_config !== '') {
            $node->custom_config = $custom_config;
        } else {
            $node->custom_config = '{}';
        }

        $node->node_speedlimit = $request->getParam('node_speedlimit');
        $node->type = $request->getParam('type') === 'true' ? 1 : 0;
        $node->sort = $request->getParam('sort');
        $node->node_class = $request->getParam('node_class');
        $node->node_bandwidth_limit = Tools::gbToB($request->getParam('node_bandwidth_limit'));
        $node->bandwidthlimit_resetday = $request->getParam('bandwidthlimit_resetday');

        if (! $node->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '修改失败',
            ]);
        }

        if (Config::obtain('im_bot_group_notify_update_node')) {
            try {
                Notification::notifyUserGroup(
                    str_replace(
                        '%node_name%',
                        $request->getParam('name'),
                        I18n::trans('bot.node_updated', $_ENV['locale'])
                    )
                );
            } catch (TelegramSDKException | GuzzleException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '修改成功，但 IM Bot 通知失败',
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

        if (Config::obtain('im_bot_group_notify_delete_node')) {
            try {
                Notification::notifyUserGroup(
                    str_replace(
                        '%node_name%',
                        $node->name,
                        I18n::trans('bot.node_deleted', $_ENV['locale'])
                    )
                );
            } catch (TelegramSDKException | GuzzleException) {
                return $response->withJson([
                    'ret' => 1,
                    'msg' => '删除成功，但 IM Bot 通知失败',
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
            $node->op = '<button class="btn btn-red" id="delete-node-' . $node->id . '" 
            onclick="deleteNode(' . $node->id . ')">删除</button>
            <button class="btn btn-orange" id="copy-node-' . $node->id . '" 
            onclick="copyNode(' . $node->id . ')">复制</button>
            <a class="btn btn-primary" href="/admin/node/' . $node->id . '/edit">编辑</a>';
            $node->type = $node->type();
            $node->sort = $node->sort();
            $node->is_dynamic_rate = $node->isDynamicRate();
            $node->dynamic_rate_type = $node->dynamicRateType();
            $node->node_bandwidth = round(Tools::bToGB($node->node_bandwidth), 2);
            $node->node_bandwidth_limit = Tools::bToGB($node->node_bandwidth_limit);
        }

        return $response->withJson([
            'nodes' => $nodes,
        ]);
    }

    private function validateCustomConfig(int $sort, string $raw): ?string
    {
        $config = json_decode($raw, true);
        if (! is_array($config) || ! is_object(json_decode($raw))) {
            return 'custom_config 必须是有效的 JSON 对象';
        }
        if ($sort === 11 && \App\Services\Subscribe\Vless::enabled($config)) {
            try {
                \App\Services\Subscribe\Vless::client($config, '');
                if (filter_var(
                    $config['offset_port_node'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1, 'max_range' => 65535]]
                ) === false) {
                    return 'VLESS 的 offset_port_node 必须是 1-65535 的端口';
                }
            } catch (\InvalidArgumentException | \RuntimeException $error) {
                return 'VLESS custom_config 无效：' . $error->getMessage();
            }
        }
        if ($sort !== 15) {
            return null;
        }
        $port = filter_var($config['offset_port_node'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]);
        if ($port === false) {
            return 'Hysteria2 的 offset_port_node 必须是 1-65535 的端口';
        }
        $objects = json_decode($raw);
        $hysteria = $config['hysteria2'] ?? null;
        if (! is_object($objects->hysteria2 ?? null) || ($hysteria['version'] ?? 2) !== 2) {
            return 'Hysteria2 custom_config 必须包含 hysteria2.version=2';
        }
        if (isset($hysteria['version']) && ! is_int($hysteria['version'])) {
            return 'Hysteria2 version 必须是整数 2';
        }
        $idle = $hysteria['udpIdleTimeout'] ?? 0;
        if (! is_int($idle) || ($idle !== 0 && ($idle < 2 || $idle > 600))) {
            return 'udpIdleTimeout 必须为 0 或 2-600 的整数';
        }
        if (isset($config['offset_port_user']) && filter_var($config['offset_port_user'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]) === false) {
            return 'offset_port_user 必须是 1-65535 的端口';
        }
        if (isset($hysteria['finalmask']) && ! is_object($objects->hysteria2->finalmask)) {
            return 'finalmask 必须是对象';
        }
        $masks = $hysteria['finalmask']['udp'] ?? [];
        if (! is_array($masks) || ! array_is_list($masks)) {
            return 'finalmask.udp 必须是数组';
        }
        foreach ($masks as $mask) {
            if (! is_array($mask) || ! is_string($mask['type'] ?? null)) {
                return 'finalmask.udp 的每项必须包含 type';
            }
            if (($mask['type'] ?? '') === 'salamander' &&
                (! is_string($mask['settings']['password'] ?? null) || strlen($mask['settings']['password']) < 4)) {
                return 'Salamander 密码必须至少包含 4 字节';
            }
        }
        $hop = \App\Services\Subscribe\Hysteria2::portHopping($config);
        $hopObject = $objects->portHopping ?? $objects->hysteria2->portHopping ??
            $objects->hysteria2->finalmask->quicParams->udpHop ?? null;
        if ($hop !== null && ! is_object($hopObject)) {
            return 'portHopping 必须是对象';
        }
        foreach (['enabled', 'enable', 'autoConfigureFirewall'] as $key) {
            if (isset($hop[$key]) && ! is_bool($hop[$key])) {
                return 'portHopping 开关必须使用 JSON 布尔值';
            }
        }
        if (isset($hop['enabled'], $hop['enable']) && $hop['enabled'] !== $hop['enable']) {
            return 'portHopping enabled 与 enable 不能互相矛盾';
        }
        if (is_array($hop) && ($hop['enabled'] ?? ($hop['enable'] ?? false))) {
            if (! is_string($hop['ports'] ?? null)) {
                return 'portHopping.ports 必须是字符串';
            }
            $ports = $hop['ports'];
            if ($ports === '' || preg_match('/^\d{1,5}(?:-\d{1,5})?(?:,\s*\d{1,5}(?:-\d{1,5})?)*$/D', $ports) !== 1) {
                return 'Hysteria2 端口跳跃 ports 格式无效';
            }
            foreach (preg_split('/,\s*/', $ports) as $range) {
                $bounds = array_map('intval', explode('-', $range));
                $last = $bounds[1] ?? $bounds[0];
                if ($bounds[0] < 1 || $last > 65535 || $last < $bounds[0]) {
                    return 'Hysteria2 端口跳跃 ports 必须位于 1-65535 且范围递增';
                }
            }
        }

        return null;
    }
}
