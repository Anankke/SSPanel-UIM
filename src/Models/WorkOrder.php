<?php
namespace App\Models;

class WorkOrder extends Model
{
    protected $connection = 'default';
    protected $table = 'work_order';

    public function getCreatedAtAttribute($value)
    {
        return date('m-d H:i', $value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('m-d H:i', $value);
    }

    public function getClosedAtAttribute($value)
    {
        return ($value == null) ? 'null' : date('y-m-d H:i', $value);
    }

    public function getClosedByAttribute($value)
    {
        return ($value == null) ? '开启中' : '已关闭';
    }
}
