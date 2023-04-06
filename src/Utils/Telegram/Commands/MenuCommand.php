<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Utils\Telegram\Callback;
use App\Utils\Telegram\TelegramTools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function json_encode;

/**
 * Class MenuCommand.
 */
final class MenuCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'menu';

    /**
     * @var string Command Description
     */
    protected string $description = '[私聊]     用户主菜单、个人中心.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID > 0) {
            // 私人会话

            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            // 触发用户
            $SendUser = [
                'id' => $Message->getFrom()->getId(),
            ];

            $user = TelegramTools::getUser($SendUser['id']);
            if ($user === null) {
                $reply = Callback::getGuestIndexKeyboard();
            } else {
                $reply = Callback::getUserIndexKeyboard($user);
            }

            // 回送信息
            return $this->replyWithMessage(
                [
                    'text' => $reply['text'],
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $reply['keyboard'],
                        ]
                    ),
                ]
            );
        }

        return null;
    }
}
