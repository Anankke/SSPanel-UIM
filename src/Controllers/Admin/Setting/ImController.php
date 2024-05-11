<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\I18n;
use App\Services\IM\Discord;
use App\Services\IM\Slack;
use App\Services\IM\Telegram;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class ImController extends BaseController
{
    private static array $update_field = [
        // TODO: rename these to im service independent
        'im_bot_group_notify_add_node',
        'im_bot_group_notify_update_node',
        'im_bot_group_notify_delete_node',
        'im_bot_group_notify_node_gfwed',
        'im_bot_group_notify_node_ungfwed',
        'im_bot_group_notify_node_online',
        'im_bot_group_notify_node_offline',
        'im_bot_group_notify_daily_job',
        'im_bot_group_notify_diary',
        'im_bot_group_notify_ann_create',
        'im_bot_group_notify_ann_update',
        // Telegram
        'telegram_token',
        'telegram_bot',
        'telegram_chatid',
        'enable_telegram_group_notify',
        'telegram_unbind_kick_member',
        'telegram_group_bound_user',
        'enable_welcome_message',
        'telegram_group_quiet',
        'allow_to_join_new_groups',
        'group_id_allowed_to_join',
        'help_any_command',
        // Discord
        'discord_bot_token',
        'discord_client_id',
        'discord_client_secret',
        'discord_guild_id',
        'discord_channel_id',
        'enable_discord_channel_notify',
        // Slack
        'slack_token',
        'slack_client_id',
        'slack_client_secret',
        'slack_team_id',
        'slack_channel_id',
        'enable_slack_channel_notify',
    ];

    private static string $success_msg = '测试信息发送成功';
    private static string $err_msg = '测试信息发送失败';

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('im');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->fetch('admin/setting/im.tpl')
        );
    }

    public function save(ServerRequest $request, Response $response, array $args): ResponseInterface
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

    public function resetWebhookToken(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $type = $args['type'];

        if ($type === 'telegram') {
            Config::set('telegram_webhook_token', Tools::genRandomChar(32));

            return $response->withJson([
                'ret' => 1,
                'msg' => 'Successfully reset webhook token',
                'data' => [
                    'telegram_webhook_token' => Config::obtain('telegram_webhook_token'),
                ],
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => 'Unknown webhook type',
        ]);
    }

    public function setWebhook(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $type = $args['type'];

        if ($type === 'telegram') {
            $webhook_url = $_ENV['baseUrl'] . '/callback/telegram?token=' . Config::obtain('telegram_webhook_token');

            try {
                $telegram = new Api($request->getParam('bot_token'));
                $telegram->removeWebhook();
                $telegram->setWebhook(['url' => $webhook_url]);

                return $response->withJson([
                    'ret' => 1,
                    'msg' => 'Successfully set telegram bot @' . $telegram->getMe()->getUsername(),
                ]);
            } catch (TelegramSDKException) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => 'Failed to set telegram bot',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => 'Unknown webhook type',
        ]);
    }

    public function testTelegram(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        try {
            (new Telegram())->send(
                (int) $request->getParam('telegram_chat_id'),
                I18n::trans('bot.test_message', $_ENV['locale']),
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

    public function testDiscord(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        try {
            (new Discord())->send(
                (int) $request->getParam('discord_channel_id'),
                I18n::trans('bot.test_message', $_ENV['locale']),
            );
        } catch (GuzzleException|Exception $e) {
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

    public function testSlack(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        try {
            (new Slack())->send(
                (int) $request->getParam('slack_channel_id'),
                I18n::trans('bot.test_message', $_ENV['locale']),
            );
        } catch (GuzzleException|Exception $e) {
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
}
