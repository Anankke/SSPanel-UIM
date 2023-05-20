<?php

declare(strict_types=1);

namespace App\Models;

/**
 * InviteCode Model
 */
final class InviteCode extends Model
{
    protected $connection = 'default';
    protected $table = 'user_invite_code';

    public function reward(): void
    {
        $user = User::where('id', $this->user_id)->first();
        $user->transfer_enable += Setting::obtain('invitation_to_register_traffic_reward') * 1024 * 1024 * 1024;

        if ($user->invite_num > 0) {
            --$user->invite_num;
            // 避免设置为不限制邀请次数的值 -1 发生变动
        }

        $user->save();
    }
}
