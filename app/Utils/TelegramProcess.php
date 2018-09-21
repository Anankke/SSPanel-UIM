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
                case 'traffic':
                    $bot->sendMessage($message->getChat()->getId(), "您当前的流量状况：
今日已使用 ".$user->TodayusedTraffic()." ".number_format(($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100, 2)."%
今日之前已使用 ".$user->LastusedTraffic()." ".number_format($user->last_day_t/$user->transfer_enable*100, 2)."%
未使用 ".$user->unusedTraffic()." ".number_format(($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100, 2)."%
					                        ", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                    break;
                case 'checkin':
                    if (!$user->isAbleToCheckin()) {
                        $bot->sendMessage($message->getChat()->getId(), "您今天已经签过到了！", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                        break;
                    }
                    $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
                    $user->transfer_enable = $user->transfer_enable + Tools::toMB($traffic);
                    $user->last_check_in_time = time();
                    $user->save();
                    $bot->sendMessage($message->getChat()->getId(), "签到成功！获得了 ".$traffic." MB 流量！", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                    break;
	    	case 'prpr':
		    $prpr = array('⁄(⁄ ⁄•⁄ω⁄•⁄ ⁄)⁄', '(≧ ﹏ ≦)', '(*/ω＼*)', 'ヽ(*。>Д<)o゜', '(つ ﹏ ⊂)', '( >  < )');
                    $bot->sendMessage($message->getChat()->getId(), $prpr[mt_rand(0,5)], $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
                    break;
                default:
                    $bot->sendMessage($message->getChat()->getId(), "???", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
            }
        } else {
            $bot->sendMessage($message->getChat()->getId(), "您未绑定本站账号。", $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
        }
    }


    public static function telegram_process($bot, $message, $command)
    {
        $user = User::where('telegram_id', $message->getFrom()->getId())->first();

        if ($message->getChat()->getId() > 0) {
            //个人
            $commands = array("ping", "chat", "traffic", "checkin", "help");
            if(in_array($command, $commands)){
                $bot->sendChatAction($message->getChat()->getId(), 'typing');
            }
            switch ($command) {
                case 'ping':
                    $bot->sendMessage($message->getChat()->getId(), 'Pong!这个群组的 ID 是 '.$message->getChat()->getId().'!');
                    break;
                case 'chat':
                    $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), substr($message->getText(), 5)));
                    break;
                case 'traffic':
                    TelegramProcess::needbind_method($bot, $message, $command, $user);
                    break;
                case 'checkin':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
	    	case 'prpr':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'help':
                    $help_list = "命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到续命
						/help 获取帮助信息

						您可以在面板里点击 资料编辑 ，滑到页面最下方，就可以看到 Telegram 绑定指示了，绑定您的账号，更多精彩功能等着您去发掘。
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
                                    $bot->sendMessage($message->getChat()->getId(), "登录验证失败，您未绑定本站账号。".substr($qrcode_text, 12));
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
                                $bot->sendMessage($message->getChat()->getId(), "登录验证失败，您未绑定本站账号。");
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
            $commands = array("ping", "chat", "traffic", "checkin", "help");
            if(in_array($command, $commands)){
                $bot->sendChatAction($message->getChat()->getId(), 'typing');
            }
            switch ($command) {
                case 'ping':
                    $bot->sendMessage($message->getChat()->getId(), 'Pong!这个群组的 ID 是 '.$message->getChat()->getId().'!', $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                    break;
                case 'chat':
                    if ($message->getChat()->getId() == Config::get('telegram_chatid')) {
                        $bot->sendMessage($message->getChat()->getId(), Tuling::chat($message->getFrom()->getId(), substr($message->getText(), 5)), $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                    } else {
                        $bot->sendMessage($message->getChat()->getId(), '不约，叔叔我们不约。', $parseMode = null, $disablePreview = false, $replyToMessageId = $message->getMessageId());
                    }
                    break;
                case 'traffic':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'checkin':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
	    	case 'prpr':
                    TelegramProcess::needbind_method($bot, $message, $command, $user, $message->getMessageId());
                    break;
                case 'help':
                    $help_list_group = "命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到续命
						/help 获取帮助信息

						您可以在面板里点击 资料编辑 ，滑到页面最下方，就可以看到 Telegram 绑定指示了，绑定您的账号，更多精彩功能等着您去发掘。
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
                    if ($message->getNewChatMember() != null && Config::get('enable_welcome_message') == 'true') {
                        $bot->sendMessage($message->getChat()->getId(), "欢迎 ".$message->getNewChatMember()->getFirstName()." ".$message->getNewChatMember()->getLastName(), $parseMode = null, $disablePreview = false);
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

            $command_list = array("ping", "chat" ,"traffic", "help", "prpr", "checkin");
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
