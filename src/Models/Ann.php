<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id         公告ID
 * @property string $date       公告日期
 * @property string $content    公告内容
 *
 * @mixin Builder
 */
final class Ann extends Model
{
    protected $connection = 'default';
    protected $table = 'announcement';
}
