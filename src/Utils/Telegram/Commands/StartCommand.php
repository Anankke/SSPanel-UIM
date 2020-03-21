<?php

namespace App\Utils\Telegram\Commands;

use App\Models\User;
use App\Utils\Telegram\TelegramTools;
use App\Utils\TelegramSessionManager;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class StratCommand.
 */
class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = '[群组/私聊] Bot 初始命令.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
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
                'id'       => $Message->getFrom()->getId(),
                'name'     => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
                'username' => $Message->getFrom()->getUsername(),
            ];
            // 消息内容
            $MessageText = trim($arguments);
            if (
                $MessageText != ''
                && TelegramTools::getUser($SendUser['id']) == null
                && strlen($MessageText) == 16
            ) {
                // 新用户绑定
                return $this->bindingAccount($SendUser, $MessageText);
            }
            // 回送信息
            $this->replyWithMessage(
                [
                    'text'       => '发送 /help 获取帮助',
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            // 群组

            if ($_ENV['enable_delete_user_cmd'] === true) {
                TelegramTools::DeleteMessage([
                    'chatid'      => $ChatID,
                    'messageid'   => $Message->getMessageId(),
                ]);
            }

            if ($_ENV['telegram_group_quiet'] === true) {
                // 群组中不回应
                return;
            }

            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => '喵喵喵.',
                ]
            );
            // 消息删除任务
            TelegramTools::DeleteMessage([
                'chatid'      => $ChatID,
                'messageid'   => $response->getMessageId(),
            ]);
        }
    }

    public function bindingAccount($SendUser, $MessageText)
    {
        $Uid = TelegramSessionManager::verify_bind_session($MessageText);
        if ($Uid == 0) {
            $text = '绑定失败了呢，经检查发现：【' . $MessageText . '】的有效期为 10 分钟，您可以在我们网站上的 **资料编辑** 页面刷新后重试.';
        } else {
            $BinsUser              = User::where('id', $Uid)->first();
            $BinsUser->telegram_id = $SendUser['id'];
            $BinsUser->im_type     = 4;
            $BinsUser->im_value    = $SendUser['username'];
            $BinsUser->save();
            if ($BinsUser->is_admin >= 1) {
                $text = '尊敬的**管理员**您好，恭喜绑定成功。' . PHP_EOL . '当前绑定邮箱为：' . $BinsUser->email;
            } else {
                if ($BinsUser->class >= 1) {
                    $text = '尊敬的 **VIP ' . $BinsUser->class . '** 用户您好.' . PHP_EOL . '恭喜您绑定成功，当前绑定邮箱为：' . $BinsUser->email;
                } else {
                    $text = '绑定成功了，您的邮箱为：' . $BinsUser->email;
                }
            }
        }
        // 回送信息
        $this->replyWithMessage(
            [
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]
        );
    }
}
