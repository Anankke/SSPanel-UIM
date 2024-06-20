<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function json_encode;
use function time;

/**
 * @property int    $id       记录ID
 * @property string $to_email 收件人邮箱
 * @property string $subject  邮件主题
 * @property string $template 邮件模板
 * @property string $array    模板内容
 * @property int    $time     添加时间
 *
 * @mixin Builder
 */
final class EmailQueue extends Model
{
    protected $connection = 'default';
    protected $table = 'email_queue';

    public function add($to, $subject, $template, $array): void
    {
        $this->to_email = $to;
        $this->subject = $subject;
        $this->template = $template;
        $this->time = time();
        $this->array = json_encode($array);
        $this->save();
    }
}
