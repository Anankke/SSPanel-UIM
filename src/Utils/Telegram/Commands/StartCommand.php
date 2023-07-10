<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Utils\Telegram;
use App\Utils\Telegram\TelegramTools;
use RedisException;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function strlen;

/**
 * Class StratCommand.
 */
final class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'start';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] Bot 初始命令.';

    public function handle(): void
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
                'name' => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
                'username' => $Message->getFrom()->getUsername(),
            ];
            // 消息内容
            $MessageText = explode(' ', trim($Message->getText()));
            $MessageKey = array_splice($MessageText, -1)[0];
            if (
                $MessageKey !== ''
                && TelegramTools::getUser($SendUser['id']) === null
                && strlen($MessageKey) === 16
            ) {
                // 新用户绑定
                $this->bindingAccount($SendUser, $MessageKey);
            }
            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => '发送 /help 获取帮助',
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            if (! Setting::obtain('telegram_group_quiet')) {
                // 发送 '输入中' 会话状态
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => '喵喵喵.',
                        'parse_mode' => 'Markdown',
                        'reply_to_message_id' => $Message->getMessageId(),
                    ]
                );
            }
        }
    }

    /**
     * @throws RedisException
     */
    public function bindingAccount($SendUser, $MessageText): void
    {
        $Uid = Telegram::verifyBindSession($MessageText);
        if ($Uid === 0) {
            $text = '绑定失败了呢，经检查发现：【' . $MessageText . '】的有效期为 10 分钟，你可以在我们网站上的 **资料编辑** 页面刷新后重试.';
        } else {
            $BinsUser = User::where('id', $Uid)->first();
            $BinsUser->telegram_id = $SendUser['id'];
            $BinsUser->im_type = 4;
            if ($SendUser['username'] === null) {
                $BinsUser->im_value = '用戶名未设置';
            } else {
                $BinsUser->im_value = $SendUser['username'];
            }
            $BinsUser->save();
            if ($BinsUser->is_admin === 1) {
                $text = '尊敬的 **管理员** 你好，恭喜绑定成功。' . PHP_EOL . '当前绑定邮箱为： ' . $BinsUser->email;
            } else {
                if ($BinsUser->class >= 1) {
                    $text = '尊敬的 **VIP ' . $BinsUser->class . '** 用户你好.' . PHP_EOL . '恭喜你绑定成功，当前绑定邮箱为： ' . $BinsUser->email;
                } else {
                    $text = '绑定成功了，你的邮箱为：' . $BinsUser->email;
                }
            }
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
