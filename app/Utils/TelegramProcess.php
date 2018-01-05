<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;

class TelegramProcess
{
    private static function needbind_method($bot, $message, $command, $user, $reply_to = null)
    { 
        if ($user != null) {
            switch ($command) {
                case 'cq':
                    $bot->sendMessage($message->getChat()->getId(), "您当月流量状况：
今日已使用 ".$user->TodayusedTraffic()." ".number_format(($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100, 2)."%
总共已使用 ".$user->LastusedTraffic()." ".number_format($user->last_day_t/$user->transfer_enable*100, 2)."%
剩余 ".$user->unusedTraffic()." ".number_format(($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100, 2)."%
当前在线设备数 ".$user->online_ip_count()." 台
					", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                    break;
                case 'qd':
                    if (!$user->isAbleToCheckin()) {
                        $bot->sendMessage($message->getChat()->getId(), "仟佰星云:您已经签到过了哦", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                        break;
                    }
                    $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
                    $user->transfer_enable = $user->transfer_enable + Tools::toMB($traffic);
                    $user->last_check_in_time = time();
                    $user->save();
                    $bot->sendMessage($message->getChat()->getId(), "走出屏障直达星云之上，只为了与你相遇！你获得了 ".$traffic." MB 流量！", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                    break;
                default:
                    $bot->sendMessage($message->getChat()->getId(), "???", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
            }
        } else {
            $bot->sendMessage($message->getChat()->getId(), "您未绑定仟佰星云账号。请/help查看绑定步骤", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
        }
    }


    public static function telegram_process($bot, $message, $command)
    {
        $bot->sendChatAction($message->getChat()->getId(), 'typing');

        $user = User::where('telegram_id', $message->getFrom()->getId())->first();

        if ($message->getChat()->getId() > 0) {
            //个人

            switch ($command) {
                case 'ping':
                    $bot->sendMessage($message->getChat()->getId(), 'Pong!仟佰星云的 群组ID 是 '.$message->getChat()->getId().',这个一般人没啥用!');
                    break;
                case 'tb':
                $bot->sendMessage($message->getChat()->getId(),"大爷，不支持淘宝了，请直接网站充值！");
                   // $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), substr($message->getText(), 5)));
                    break;
                case 'cq':
                    TelegramProcess::needbind_method($bot, $message, $command, $user);
                    break;
                case 'qd':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'help':
                    $help_list = "命令列表：
						/ping  获取群组ID
						/tb 淘宝购买
						/cq 查询流量
						/qd 签到获取流量
						/help 获取帮助信息

						绑定提示:您可以在网站里点击->资料编辑->滑到页面最下方->Telegram绑定->把二维码拍下来单独发送给TG机器人->绑定成功，机器人扫码登陆，数字登陆、签到等更多精彩功能等着您去发掘。
					";
                    $bot->sendMessage($message->getChat()->getId(), $help_list);
                    break;
                default:
                    if ($message->getPhoto() != null) {
                        $bot->sendMessage($message->getChat()->getId(), "正在解码，请稍候。。。");
                        $bot->sendChatAction($message->getChat()->getId(), 'typing');

                        $photos = $message->getPhoto();

                        $photo_size_array = array();
                        $photo_id_array = array();
                        $photo_id_list_array = array();


                        foreach ($photos as $photo) {
                            $file = $bot->getFile($photo->getFileId());
                            $real_id = substr($file->getFileId(), 0, 36);
                            if (!isset($photo_size_array[$real_id])) {
                                $photo_size_array[$real_id] = 0;
                            }

                            if ($photo_size_array[$real_id] < $file->getFileSize()) {
                                $photo_size_array[$real_id] = $file->getFileSize();
                                $photo_id_array[$real_id] = $file->getFileId();
                                if (!isset($photo_id_list_array[$real_id])) {
                                    $photo_id_list_array[$real_id] = array();
                                }

                                array_push($photo_id_list_array[$real_id], $file->getFileId());
                            }
                        }

                        foreach ($photo_id_array as $key => $value) {
                            $file = $bot->getFile($value);
                            $qrcode_text = QRcode::decode("https://api.telegram.org/file/bot".Config::get('telegram_token')."/".$file->getFilePath());

                            if ($qrcode_text == null) {
                                foreach ($photo_id_list_array[$key] as $fail_key => $fail_value) {
                                    $fail_file = $bot->getFile($fail_value);
                                    $qrcode_text = QRcode::decode("https://api.telegram.org/file/bot".Config::get('telegram_token')."/".$fail_file->getFilePath());
                                    if ($qrcode_text != null) {
                                        break;
                                    }
                                }
                            }

                            if (substr($qrcode_text, 0, 11) == 'mod://bind/' && strlen($qrcode_text) == 27) {
                                $uid = TelegramSessionManager::verify_bind_session(substr($qrcode_text, 11));
                                if ($uid != 0) {
                                    $user = User::where('id', $uid)->first();
                                    $user->telegram_id = $message->getFrom()->getId();
                                    $user->im_type = 4;
                                    $user->im_value = $message->getFrom()->getUsername();
                                    $user->save();
                                    $bot->sendMessage($message->getChat()->getId(), "绑定成功。邮箱：".$user->email);
                                } else {
                                    $bot->sendMessage($message->getChat()->getId(), "绑定失败，二维码无效。".substr($qrcode_text, 11));
                                }
                            }

                            if (substr($qrcode_text, 0, 12) == 'mod://login/' && strlen($qrcode_text) == 28) {
                                if ($user != null) {
                                    $uid = TelegramSessionManager::verify_login_session(substr($qrcode_text, 12), $user->id);
                                    if ($uid != 0) {
                                        $bot->sendMessage($message->getChat()->getId(), "登录验证成功。邮箱：".$user->email);
                                    } else {
                                        $bot->sendMessage($message->getChat()->getId(), "登录验证失败，二维码无效。".substr($qrcode_text, 12));
                                    }
                                } else {
                                    $bot->sendMessage($message->getChat()->getId(), "登录验证失败，您未绑定仟佰星云账号。".substr($qrcode_text, 12));
                                }
                            }

                            break;
                        }
                    } else {
                        if (is_numeric($message->getText()) && strlen($message->getText()) == 6) {
                            if ($user != null) {
                                $uid = TelegramSessionManager::verify_login_number($message->getText(), $user->id);
                                if ($uid != 0) {
                                    $bot->sendMessage($message->getChat()->getId(), "登录验证成功。邮箱：".$user->email);
                                } else {
                                    $bot->sendMessage($message->getChat()->getId(), "登录验证失败，数字无效。");
                                }
                            } else {
                                $bot->sendMessage($message->getChat()->getId(), "登录验证失败，您未绑定仟佰星云账号。");
                            }
                            break;
                        }
                        $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), $message->getText()));
                    }
            }
        } else {
            //群组
            if (Config::get('telegram_group_quiet') == 'true') {
                return;
            }

            switch ($command) {
                case 'ping':
                    $bot->sendMessage($message->getChat()->getId(), 'Pong!这个群组的 ID 是 '.$message->getChat()->getId().'!', $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                    break;
                case 'tb':
                    $bot->sendMessage($message->getChat()->getId(),"大爷，不支持淘宝了，请直接捐献！");
                   // if ($message->getChat()->getId() == Config::get('telegram_chatid')) {
                   //     $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), substr($message->getText(), 5)), $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                //    } else {
                  //      $bot->sendMessage($message->getChat()->getId(), '不约，叔叔我们不约。', $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                 //   }
                    break;
                case 'cq':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'qd':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'help':
                    $help_list_group = "命令列表：
						/ping  获取群组ID
						/tb 淘宝购买
						/cq 查询流量
						/qd 签到获取流量
						/help 获取帮助信息

						绑定提示:您可以在网站里点击->资料编辑->滑到页面最下方->Telegram绑定->把二维码拍下来单独发送给TG机器人->绑定成功，机器人扫码登陆，数字登陆、签到等更多精彩功能等着您去发掘。
					";
                    $bot->sendMessage($message->getChat()->getId(), $help_list_group, $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                    break;
                default:
                    if ($message->getText() != null) {
                        if ($message->getChat()->getId() == Config::get('telegram_chatid')) {
                            $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), $message->getText()), $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                        } else {
                            $bot->sendMessage($message->getChat()->getId(), '不约，叔叔我们不约。', $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                        }
                    }
            }
        }

        $bot->sendChatAction($message->getChat()->getId(), '');
    }

    public static function process()
    {
        try {
            $bot = new \TelegramBot\Api\Client(Config::get('telegram_token'));
            // or initialize with botan.io tracker api key
            // $bot = new \TelegramBot\Api\Client('YOUR_BOT_API_TOKEN', 'YOUR_BOTAN_TRACKER_API_KEY');

            $command_list = array("ping", "tb" ,"cq", "help", "qd");
            foreach ($command_list as $command) {
                $bot->command($command, function ($message) use ($bot, $command) {
                    TelegramProcess::telegram_process($bot, $message, $command);
                });
            }

            $bot->on($bot->getEvent(function ($message) use ($bot) {
                TelegramProcess::telegram_process($bot, $message, '');
            }), function () {
                return true;
            });

            $bot->run();
        } catch (\TelegramBot\Api\Exception $e) {
            $e->getMessage();
        }
    }
}
