<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Notification;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Query\Builder;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function time;

/**
 * @property int    $id       记录ID
 * @property int    $userid   用户ID
 * @property string $ip       登录IP
 * @property int    $datetime 登录时间
 * @property int    $type     登录类型
 *
 * @mixin Builder
 */
final class LoginIp extends Model
{
    protected $connection = 'default';
    protected $table = 'login_ip';

    /**
     * 登录用户
     */
    public function user(): ?User
    {
        return (new User())->find($this->userid);
    }

    /**
     * 登录成功与否
     */
    public function type(): string
    {
        return $this->type === 0 ? '成功' : '失败';
    }

    /**
     * 记录登录 IP
     *
     * @param string $ip IP
     * @param int $type 1 = failed, 0 = success
     * @param int $user_id User ID
     */
    public function collectLoginIP(string $ip, int $type = 0, int $user_id = 0): void
    {
        if (Config::obtain('login_log')) {
            $this->ip = $ip;
            $this->userid = $user_id;
            $this->datetime = time();
            $this->type = $type;

            if (Config::obtain('notify_new_login') &&
                $user_id !== 0 &&
                (new LoginIp())->where('userid', $user_id)->where('ip', $this->ip)->count() === 0
            ) {
                try {
                    Notification::notifyUser(
                        (new User())->where('id', $user_id)->first(),
                        $_ENV['appName'] . '-新登录通知',
                        '你的账号于 ' . date('Y-m-d H:i:s') . ' 通过 ' . $this->ip . ' 地址登录了用户面板',
                    );
                } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                    echo $e->getMessage();
                }
            }

            $this->save();
        }
    }
}
