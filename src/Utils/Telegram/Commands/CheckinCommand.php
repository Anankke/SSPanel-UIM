<?php
namespace App\Utils\Telegram\Commands;

use App\Models\User;
use App\Utils\Tools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class CheckinCommand.
 */
class CheckinCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'checkin';

    /**
     * @var string Command Description
     */
    protected $description = '[群组/私聊] 每日签到.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID < 0) {
            if ($_ENV['telegram_group_quiet'] === true) {
                // 群组中不回应
                return;
            }
            if ($ChatID != $_ENV['telegram_chatid']) {
                // 非我方群组
                return;
            }
        }

        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // 触发用户
        $SendUser = [
            'id' => $Message->getFrom()->getId(),
            'name' => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
            'username' => $Message->getFrom()->getUsername(),
        ];

        $User = User::where('telegram_id', $SendUser['id'])->first();
        if ($User == null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => '需要先在用户中心的资料编辑页面绑定你的账户，然后才能签到哦',
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            /* $checkin = $User->checkin();
            // 回送信息
            $response = $this->replyWithMessage(
            [
            'text'                  => $checkin['msg'],
            'reply_to_message_id'   => $Message->getMessageId(),
            'parse_mode'            => 'Markdown',
            ]
            ); */
            if ($_ENV['enable_checkin'] == false) {
                $msg = '暂时不能签到';
            } else {
                if ($_ENV['enable_expired_checkin'] == false && strtotime($User->expire_in) < time()) {
                    $msg = '账户过期时不能签到';
                } else {
                    if (!$User->isAbleToCheckin()) {
                        $msg = '今天已经签到过了';
                    } else {
                        $rand_traffic = random_int((int) $_ENV['checkinMin'], (int) $_ENV['checkinMax']);
                        $User->transfer_enable += Tools::toMB($rand_traffic);
                        $User->last_check_in_time = time();
                        if ($_ENV['checkin_add_time']) {
                            $add_timestamp = $_ENV['checkin_add_time_hour'] * 3600;
                            if (time() > strtotime($User->expire_in)) {
                                $User->expire_in = date('Y-m-d H:i:s', time() + $add_timestamp);
                            } else {
                                $User->expire_in = date('Y-m-d H:i:s', strtotime($User->expire_in) + $add_timestamp);
                            }
                        }
                        $User->save();
                        $msg = '签到获得了 ' . $rand_traffic . ' MB 流量';
                    }
                }
            }

            $response = $this->replyWithMessage(
                [
                    'text' => $msg,
                    'reply_to_message_id' => $Message->getMessageId(),
                    'parse_mode' => 'Markdown',
                ]
            );
        }
        return $response;
    }
}
