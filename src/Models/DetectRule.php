<?php
namespace App\Models;

class DetectRule extends Model
{
    protected $connection = 'default';
    protected $table = 'detect_list';

    public function type(): string
    {
        return $this->type == 1 ? '数据包明文匹配' : '数据包十六进制匹配';
    }
}
