<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id    审计规则ID
 * @property string $name  规则名称
 * @property string $text  规则介绍
 * @property string $regex 规则内容
 * @property int    $type  规则类型
 *
 * @mixin Builder
 */
final class DetectRule extends Model
{
    protected $connection = 'default';
    protected $table = 'detect_list';

    /**
     * 规则类型
     */
    public function type(): string
    {
        return $this->type === 1 ? '数据包明文匹配' : '数据包 hex 匹配';
    }
}
