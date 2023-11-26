<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Notification;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Query\Builder;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function time;

/**
 * @property int    $id                 记录ID
 * @property int    $user_id            用户ID
 * @property string $type               获取的订阅类型
 * @property string $request_ip         请求IP
 * @property string $request_user_agent 请求UA
 * @property int    $request_time       请求时间
 *
 * @mixin Builder
 */
final class SubscribeLog extends Model
{
    protected $connection = 'default';
    protected $table = 'subscribe_log';

    /**
     * 用户
     */
    public function user(): ?User
    {
        return (new User())->find($this->user_id);
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
     */
    public function add(User $user, string $type, string $ua): void
    {
        $this->user_id = $user->id;
        $this->type = $type;
        $this->request_ip = $_SERVER['REMOTE_ADDR'];
        $this->request_user_agent = $ua;
        $this->request_time = time();

        if (Config::obtain('notify_new_subscribe') &&
            (new SubscribeLog())->where('user_id', $this->user_id)
                ->where('request_ip', 'like', '%' . $this->request_ip . '%')
                ->count() === 0
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
