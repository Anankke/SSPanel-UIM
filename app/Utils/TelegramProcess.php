<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;
use App\Controllers\LinkController;

class TelegramProcess
{
    private static $all_rss = [
        "clean_link"=>"重置订阅",
        "?mu=0" => "SSR普通订阅" ,
        "?mu=1" => "SSR单端口订阅",
        "?mu=3"=> "SS/SSD订阅",
        "?mu=2" => "V2ray订阅",
        "?mu=4"=> "Clash订阅"];

    private static function callback_bind_method($bot,$message,$command)
	{
        $reply_to = $message->getMessageId();
        $user = User::where('telegram_id', $message->getFrom()->getId())->first();
		$reply_message="？？？";
        if ($user != null) {
            switch (true){
                case (strpos($command,"mu")):
                    $ssr_sub_token = LinkController::GenerateSSRSubCode($user->id, 0);
                    $subUrl = Config::get('subUrl');
                    $reply_message = self::$all_rss[$command].": ".$subUrl.$ssr_sub_token.$command.PHP_EOL;
                    break;
                case ($command=="clean_link"):
                    $user->clean_link();
					$reply_message="链接重置成功";
                    break;
            }
            $bot->sendMessage($message->getChat()->getId(), $reply_message, $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to);
        }
}
    private static function needbind_method($bot, $message, $command, $user, $reply_to=null)
    {
		$reply = [
			"message" => "？？？",
			"markup" => null,
		];
        if ($user != null) {
            switch ($command) {
                case 'traffic':
                    $reply['message']="您当前的流量状况：
今日已使用 " . $user->TodayusedTraffic() . " " . number_format(($user->u + $user->d - $user->last_day_t) / $user->transfer_enable * 100, 2) . "%
今日之前已使用 " . $user->LastusedTraffic() . " " . number_format($user->last_day_t / $user->transfer_enable * 100, 2) . "%
未使用 " . $user->unusedTraffic() . " " . number_format(($user->transfer_enable - ($user->u + $user->d)) / $user->transfer_enable * 100, 2) . "%";
                    break;
                case 'checkin':
                    if (!$user->isAbleToCheckin()) {
                        $reply['message']= "您今天已经签过到了！";
                        break;
                    }
                    $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
                    $user->transfer_enable = $user->transfer_enable + Tools::toMB($traffic);
                    $user->last_check_in_time = time();
                    $user->save();
                   $reply['message']= "签到成功！获得了 " . $traffic . " MB 流量！";
                    break;
                case 'prpr':
                    $prpr = array('⁄(⁄ ⁄•⁄ω⁄•⁄ ⁄)⁄', '(≧ ﹏ ≦)', '(*/ω＼*)', 'ヽ(*。>Д<)o゜', '(つ ﹏ ⊂)', '( >  < )');
                    $reply['message']= $prpr[mt_rand(0, 5)];
                    break;
                case "rss":
                    $reply['message'] = "点击以下按钮获取对应订阅: ";
                    $keys = [];
                    foreach (self::$all_rss as $key => $value){
                        $keys[] = [["text" => $value, "callback_data" => $key]];
                    }
                   $reply['mark'] = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        $keys
                    );
                    break;

            }
        } else {
             $reply['message'] = "您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号";
        }

		return $reply;
    }


    public static function telegram_process($bot, $message, $command)
    {
        $user = User::where('telegram_id', $message->getFrom()->getId())->first();
		$reply_to = $message->getMessageId();
		$reply = [
			"message" => "？？？",
			"markup" => null,
		];
        if ($message->getChat()->getId() > 0) {
            //个人
            $bot->sendChatAction($message->getChat()->getId(), 'typing');

            switch ($command) {
                case 'ping':
                    $reply['message']= 'Pong!您的 ID 是 ' . $message->getChat()->getId() . '!';
                    break;
                case 'traffic':
                case 'checkin':
                case 'prpr':
                case "rss":
                    $reply=TelegramProcess::needbind_method($bot, $message, $command, $user, $reply_to);
                    break;
                case 'help':
                    $reply['message'] = "命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到
						/help 获取帮助信息
						/rss 获取节点订阅";
					if($user==null){
						$help_list.=PHP_EOL."您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号";
					}
                    break;
                default:
					if ($message->getPhoto()==null){
						if (is_numeric($message->getText()) || strlen($message->getText()) != 6) {
							$reply['message']= Tuling::chat($message->getFrom()->getId(), $message->getText());
                            break;
                        }
						
						if($user==null){
							$reply['message']= "登录验证失败，您未绑定本站账号";
							break;
						}

                        $uid = TelegramSessionManager::verify_login_number($message->getText(), $user->id);
                        if ($uid != 0) {
                            $reply['message']= "登录验证成功，邮箱：" . $user->email;
                        } else {
                            $reply['message']= "登录验证失败，数字无效";
                        }
						break;
					}

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
						
                        if ($photo_size_array[$real_id] >= $file->getFileSize()) {
							continue;
                        }
						
                        $photo_size_array[$real_id] = $file->getFileSize();
                        $photo_id_array[$real_id] = $file->getFileId();
                        if (!isset($photo_id_list_array[$real_id])) {
                            $photo_id_list_array[$real_id] = array();
                        }

                        array_push($photo_id_list_array[$real_id], $file->getFileId());
                    }

                    foreach ($photo_id_array as $key => $value) {
                        $file = $bot->getFile($value);
                        $qrcode_text = QRcode::decode("https://api.telegram.org/file/bot" . Config::get('telegram_token') . "/" . $file->getFilePath());

                        if ($qrcode_text == null) {
                            foreach ($photo_id_list_array[$key] as $fail_key => $fail_value) {
                                $fail_file = $bot->getFile($fail_value);
                                $qrcode_text = QRcode::decode("https://api.telegram.org/file/bot" . Config::get('telegram_token') . "/" . $fail_file->getFilePath());
                                if ($qrcode_text != null) {
                                    break;
                                }
                            }
                        }

                        if (substr($qrcode_text, 0, 11) == 'mod://bind/' && strlen($qrcode_text) == 27) {
                            $uid = TelegramSessionManager::verify_bind_session(substr($qrcode_text, 11));
                            if ($uid == 0) {
								$reply['message']= "绑定失败，二维码无效：" . substr($qrcode_text, 11)."二维码的有效期为10分钟，请尝试刷新网站的“资料编辑”页面以更新二维码";
								continue;
                            }							
                            $user = User::where('id', $uid)->first();
                            $user->telegram_id = $message->getFrom()->getId();
                            $user->im_type = 4;
                            $user->im_value = $message->getFrom()->getUsername();
                            $user->save();
                            $reply['message']= "绑定成功，邮箱：" . $user->email;
							break;
                        }

                        if (substr($qrcode_text, 0, 12) == 'mod://login/' && strlen($qrcode_text) == 28) {
                            if ($user == null) {
								$reply['message']= "登录验证失败，您未绑定本站账号" . substr($qrcode_text, 12);
								break;
                            }
							
                            $uid = TelegramSessionManager::verify_login_session(substr($qrcode_text, 12), $user->id);
                            if ($uid != 0) {
                                $reply['message']= "登录验证成功，邮箱：" . $user->email;
                            } else {
                                $reply['message']= "登录验证失败，二维码无效" . substr($qrcode_text, 12);
                            }
							break;
                        }

                        break;
                    }
            }
        } else {
            //群组
            if (Config::get('telegram_group_quiet') == 'true') {
                return;
            }
            $bot->sendChatAction($message->getChat()->getId(), 'typing');

            switch ($command) {
                case 'ping':
                    $reply['message']= 'Pong!这个群组的 ID 是 ' . $message->getChat()->getId() . '!';
                    break;
                case 'traffic':
                case 'checkin':
                case 'prpr':
                    $reply=TelegramProcess::needbind_method($bot, $message, $command, $user, $reply_to);
                    break;
                case 'help':
                    $reply['message'] = "命令列表：
						/ping  获取群组ID
						/traffic 查询流量
						/checkin 签到
						/help 获取帮助信息
						/rss 获取节点订阅	";
					if($user==null){
						$reply['message'].=PHP_EOL."您未绑定本站账号，您可以进入网站的“资料编辑”，在右下方绑定您的账号";
					}
                    break;
                default:
                    if ($message->getText() != null) {
                        if ($message->getChat()->getId() == Config::get('telegram_chatid')) {
                            $reply['message']= Tuling::chat($message->getFrom()->getId(), $message->getText());
                        } else {
                            $reply['message']='不约，叔叔我们不约';
                        }
                    }
                    if ($message->getNewChatMember() != null && Config::get('enable_welcome_message') == 'true') {
                        $reply['message']="欢迎 " . $message->getNewChatMember()->getFirstName() . " " . $message->getNewChatMember()->getLastName();
                    }
					else{
						$reply['message']=null;
					}
            }
        }

		$bot->sendMessage($message->getChat()->getId(), $reply['message'] , $parseMode = null, $disablePreview = false, $replyToMessageId = $reply_to, $replyMarkup=$reply['mark']);
        $bot->sendChatAction($message->getChat()->getId(), '');
    }

    public static function process()
    {
        try {
            $bot = new \TelegramBot\Api\Client(Config::get('telegram_token'));
            // or initialize with botan.io tracker api key
            // $bot = new \TelegramBot\Api\Client('YOUR_BOT_API_TOKEN', 'YOUR_BOTAN_TRACKER_API_KEY');

            $command_list = array("ping", "traffic", "help", "prpr", "checkin", "rss");
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
            $bot->on(function($update) use ($bot){
                $callback = $update->getCallbackQuery();
                //Answer to Telegram, you make answer in the end, or in the beginning.
                $message =  $callback->getMessage();
                $message->setFrom($callback->getFrom());
                TelegramProcess::callback_bind_method($bot,$message,$callback->getData());
            }, function($update){
                $callback = $update->getCallbackQuery();
                if (is_null($callback) || !strlen($callback->getData())){return false; }
                return true;
            });

            $bot->run();
        } catch (\TelegramBot\Api\Exception $e) {
            $e->getMessage();
        }
    }
}
