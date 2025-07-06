<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id      文档ID
 * @property int    $status  文档状态
 * @property int    $sort    文档排序
 * @property string $date    文档日期
 * @property string $title   文档标题
 * @property string $content 文档内容
 *
 * @mixin Builder
 */
final class Docs extends Model
{
    protected $connection = 'default';
    protected $table = 'docs';

    public function status(): string
    {
        return match ($this->status) {
            0 => '未发布',
            1 => '已发布',
            default => '未知',
        };
    }
}
