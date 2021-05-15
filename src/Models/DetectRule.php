<?php

namespace App\Models;

/**
 * DetectLog Model
 */
class DetectRule extends Model
{
    protected $connection = 'default';

    protected $table = 'detect_list';

    /**
     * 规则类型
     */
    public function type(): string
    {
        return $this->type == 1 ? '数据包明文匹配' : '数据包十六进制匹配';
    }
}
