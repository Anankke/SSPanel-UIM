<?php

declare(strict_types=1);

namespace App\Models;

use function json_encode;
use function time;

/**
 * EmailQueue Model
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
