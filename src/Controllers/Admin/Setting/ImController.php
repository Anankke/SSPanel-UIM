<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Setting;
use App\Services\IM\Discord;
use App\Services\IM\Slack;
use App\Services\IM\Telegram;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function json_encode;

final class ImController extends BaseController
{
    public static array $update_field = [
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

    /**
     * @throws Exception
     */
    public function im($request, $response, $args)
    {
        $settings = [];
        $settings_raw = Setting::get(['item', 'value', 'type']);

        foreach ($settings_raw as $setting) {
            if ($setting->type === 'bool') {
                $settings[$setting->item] = (bool) $setting->value;
            } else {
                $settings[$setting->item] = (string) $setting->value;
            }
        }

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/im.tpl')
        );
    }

    public function saveIm($request, $response, $args)
    {
        $list = self::$update_field;

        foreach ($list as $item) {
            $setting = Setting::where('item', '=', $item)->first();

            if ($setting->type === 'array') {
                $setting->value = json_encode($request->getParam($item));
            } else {
                $setting->value = $request->getParam($item);
            }

            if (! $setting->save()) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => "保存 {$item} 时出错",
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
                '这是一条测试消息',
            );
        } catch (TelegramSDKException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试信息发送失败 ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试信息发送成功',
        ]);
    }

    public function testDiscord($request, $response, $args)
    {
        try {
            (new Discord())->send(
                $request->getParam('discord_user_id'),
                '这是一条测试消息',
            );
        } catch (GuzzleException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试信息发送失败 ' . $e->getMessage(),
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
                '这是一条测试消息',
            );
        } catch (GuzzleException|Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '测试信息发送失败 ' . $e->getMessage(),
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '测试信息发送成功',
        ]);
    }
}
