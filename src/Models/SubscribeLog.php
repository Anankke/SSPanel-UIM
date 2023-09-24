<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Notification;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use voku\helper\AntiXSS;
use function time;

final class SubscribeLog extends Model
{
    protected $connection = 'default';
    protected $table = 'subscribe_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Ip 地理位置
     */
    public function getLocationAttribute(): string
    {
        try {
            return Tools::getIpLocation($this->request_ip);
        } catch (InvalidDatabaseException|Exception $e) {
            return '未知';
        }
    }

    /**
     * 记录订阅日志
     *
     * @throws TelegramSDKException
     * @throws GuzzleException
     * @throws ClientExceptionInterface
     */
    public function add(User $user, string $type, string $ua): void
    {
        $antiXss = new AntiXSS();
        $this->user_id = $user->id;
        $this->type = $antiXss->xss_clean($type);
        $this->request_ip = $_SERVER['REMOTE_ADDR'];
        $this->request_user_agent = $antiXss->xss_clean($ua);
        $this->request_time = time();

        if (Setting::obtain('notify_new_subscribe') &&
            SubscribeLog::where('user_id', $this->user_id)->where('request_ip', 'like', $this->request_ip)->count() === 0
        ) {
            try {
                Notification::notifyUser(
                    $user,
                    $_ENV['appName'] . '-新订阅通知',
                    '你的账号于 ' . date('Y-m-d H:i:s') . ' 通过 ' . $this->request_ip . ' 地址订阅了新的节点',
                );
            } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                echo $e->getMessage();
            }
        }

        $this->save();
    }
}
