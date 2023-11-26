<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * @property int    $id     记录ID
 * @property string $type   订阅token
 * @property int    $userid 用户ID
 *
 * @mixin Builder
 */
final class Link extends Model
{
    protected $connection = 'default';
    protected $table = 'link';

    public function user(): ?User
    {
        return (new User())->find($this->attributes['userid']);
    }

    public function isValid(): bool
    {
        if ($this !== null && $this->user() !== null && $this->user()->is_banned === 0) {
            return true;
        }

        return false;
    }
}
