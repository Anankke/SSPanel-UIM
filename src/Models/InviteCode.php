<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
use Illuminate\Database\Query\Builder;

/**
 * @property int    $id         记录ID
 * @property string $code       邀请码
 * @property int    $user_id    用户ID
 *
 * @mixin Builder
 */
final class InviteCode extends Model
{
    protected $connection = 'default';
    protected $table = 'user_invite_code';

    public function reward(): void
    {
        $user = (new User())->where('id', $this->user_id)
            ->where('is_banned', 0)
            ->where('is_shadow_banned', 0)
            ->first();

        if ($user !== null) {
            $user->transfer_enable += Tools::toGB(Config::obtain('invitation_to_register_traffic_reward'));

            if ($user->invite_num > 0) {
                --$user->invite_num;
                // 避免设置为不限制邀请次数的值 -1 发生变动
            }

            $user->save();
        }
    }
}
