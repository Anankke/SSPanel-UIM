<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id         公告ID
 * @property int    $status     公告状态
 * @property int    $sort       公告排序
 * @property string $date       公告日期
 * @property string $content    公告内容
 *
 * @mixin Builder
 */
final class Ann extends Model
{
    protected $connection = 'default';
    protected $table = 'announcement';

    public function status(): string
    {
        return match ($this->status) {
            0 => '未发布',
            1 => '已发布',
            2 => '置顶',
            default => '未知',
        };
    }
}
