<?php

declare(strict_types=1);

namespace App\Models;

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
}
