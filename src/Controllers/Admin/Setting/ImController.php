<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\IM\Discord;
use App\Services\IM\Slack;
use App\Services\IM\Telegram;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class ImController extends BaseController
{
    private static array $update_field = [
        'enable_telegram',
        'telegram_token',
        'telegram_chatid',
        'telegram_bot',
        'telegram_request_token',
        'telegram_add_node',
        'telegram_add_node_text',
        'telegram_update_node',
        'telegram_update_node_text',
        'telegram_delete_node',
        'telegram_delete_node_text',
        'telegram_node_gfwed',
        'telegram_node_gfwed_text',
        'telegram_node_ungfwed',
        'telegram_node_ungfwed_text',
        'telegram_node_offline',
        'telegram_node_offline_text',
        'telegram_node_online',
        'telegram_node_online_text',
        'telegram_daily_job',
        'telegram_daily_job_text',
        'telegram_diary',
        'telegram_diary_text',
        'telegram_unbind_kick_member',
        'telegram_group_bound_user',
        'enable_welcome_message',
        'telegram_group_quiet',
        'allow_to_join_new_groups',
        'group_id_allowed_to_join',
        'help_any_command',
        'user_not_bind_reply',
        'discord_bot_token',
        'discord_client_id',
        'discord_client_secret',
        'discord_guild_id',
        'slack_token',
        'slack_client_id',
        'slack_client_secret',
        'slack_team_id',
    ];

    private static string $test_msg = '这是一条测试消息';
    private static string $success_msg = '测试信息发送成功';
    private static string $err_msg = '测试信息发送失败';

    /**
     * @throws Exception
     */
    public function index($request, $response, $args)
    {
        $settings = Config::getClass('im');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/im.tpl')
        );
    }

    public function save($request, $response, $args)
    {
        foreach (self::$update_field as $item) {
            if (! Config::set($item, $request->getParam($item))) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '保存 ' . $item . ' 时出错',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }

    public function testTelegram($request, $response, $args)
    {
        try {
            (new Telegram())->send(
                $request->getParam('telegram_user_id'),
                $this::$test_msg,
            );
        } catch (TelegramSDKException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $this::$err_msg . ' ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => $this::$success_msg,
        ]);
    }

    public function testDiscord($request, $response, $args)
    {
        try {
            (new Discord())->send(
                $request->getParam('discord_user_id'),
                $this::$test_msg,
            );
        } catch (GuzzleException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $this::$err_msg . ' ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试信息发送成功',
        ]);
    }

    public function testSlack($request, $response, $args)
    {
        try {
            (new Slack())->send(
                $request->getParam('slack_user_id'),
                $this::$test_msg,
            );
        } catch (GuzzleException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $this::$err_msg . ' ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试信息发送成功',
        ]);
    }
}
