<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;
use App\Controllers\LinkController;

class Discord{
	public static function set(){
		$loop = \React\EventLoop\Factory::create();
		$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);
		$client->on('error', function ($error) {
			echo $error.PHP_EOL;
		});
		
		$client->on('ready', function () use ($client) {
			echo 'Logged in as '.$client->user->tag.' created on '.$client->user->createdAt->format('d.m.Y H:i:s').PHP_EOL;
		});

		$client->on('message', function ($message) {
			//echo 'Received Message from '.$message->author->tag.' in '.($message->channel->type === 'text' ? 'channel #'.$message->channel->name : 'DM').' with '.$message->attachments->count().' attachment(s) and '.\count($message->embeds).' embed(s)'.PHP_EOL;
			if(strpos($message->content, '!bind') === 0) {
				bindQR($client, $message);
			}
			elseif(strpos($message->content, '!checkin') === 0) {
				checkin($client, $message);
			}
			elseif(strpos($message->content, '!traffic') === 0) {
				traffic($client, $message);
			}
		});

		$client->login(Config::get('discord_token'))->done();
		$loop->run();
		echo('设置成功！'.PHP_EOL);
	}
	
	public static function bindQR($client, $message){
		$message->channel->send("正在解码，请稍后。。。");
		$attachments=$message->attachments->toArray();
		reset($attachments);
		$qrcode_text=QRcode::decod(current($attachments)->url);
		if($qrcode_text==null){
			$message->channel->send("解码失败");
			return;
		}
		
		if (substr($qrcode_text, 0, 11) == 'mod://bind/' && strlen($qrcode_text) == 27) {
			$uid = TelegramSessionManager::verify_bind_session(substr($qrcode_text, 11));
			if ($uid == 0) {
				$message->channel->send("绑定失败，二维码无效：" . substr($qrcode_text, 11)."二维码的有效期为10分钟，请尝试刷新网站的“资料编辑”页面以更新二维码");
				return;			
			}
			$user = User::where('id', $uid)->first();
			$user->discord = $message->author->id;
			$user->im_type = 5;
			$user->im_value = $message->author->username;
			$user->save();
			$reply['message']= "绑定成功，邮箱：" . $user->email;
		}
		
		$message->channel->send("未知内容二维码");
	}

	public static function checkin($client, $message){
	
	}

	public static function traffic($client, $message){
	
	}
}