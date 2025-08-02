<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property string $id 凭证ID
 * @property int $userid 用户ID
 * @property string $name 设备名称
 * @property string $rawid 设备ID
 * @property string $body 内容
 * @property string $created_at 创建时间
 * @property string $used_at 上次使用时间
 * @property string $type 设备类型
 *
 * @mixin Builder
 */
final class MFADevice extends Model
{
    protected $connection = 'default';
    protected $table = 'mfa_devices';
}