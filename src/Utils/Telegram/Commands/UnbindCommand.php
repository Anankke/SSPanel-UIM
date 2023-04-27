<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Models\Setting;
use App\Utils\Telegram\TelegramTools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class UnbindCommand.
 */
final class UnbindCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'unbind';

    /**
     * @var string Command Description
     */
    protected string $description = '[私聊]     解除账户绑定.';

    public function handle(): void
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();
        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        // 触发用户
        $SendUser = [
            'id' => $Message->getFrom()->getId(),
        ];

        $User = TelegramTools::getUser($SendUser['id']);

        if ($ChatID > 0) {
            // 私人

            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            if ($User === null) {
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => Setting::obtain('user_not_bind_reply'),
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }

            // 消息内容
            $MessageText = explode(' ', trim($Message->getText()));
            $MessageKey = array_splice($MessageText, -1)[0];
            $text = '';

            if ($MessageKey === $User->email) {
                $temp = $User->telegramReset();
                $text = $temp['msg'];
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => $text,
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }
            if ($MessageKey !== '') {
                $text = '键入的 Email 地址与你的账户不匹配.';
            }
            if ($MessageKey === '/unbind') {
                $text = $this->sendtext();
            }

            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => $text,
                    'parse_mode' => 'Markdown',
                ]
            );
        }
    }

    public function sendtext(): string
    {
        $text = '以 `/unbind example@qq.com` 的形式发送进行解绑.';
        if (Setting::obtain('telegram_unbind_kick_member')) {
            $text .= PHP_EOL . PHP_EOL . '根据管理员的设定，你解绑账户将会被自动移出用户群.';
        }
        return $text;
    }
}
