<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id      文档ID
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
}
