<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder;
use function time;

/**
 * @property int   $id         记录ID
 * @property float $total      总金额
 * @property int   $userid     用户ID
 * @property int   $ref_by     推荐人ID
 * @property float $ref_get    推荐人获得金额
 * @property int   $invoice_id 账单ID
 * @property int   $datetime   创建时间
 *
 * @mixin Builder
 */
final class Payback extends Model
{
    protected $connection = 'default';
    protected $table = 'payback';

    public function user(): \Illuminate\Database\Eloquent\Model|User|null
    {
        return (new User())->where('id', $this->userid)->first();
    }

    public function getUserNameAttribute(): string
    {
        return (new User())->where('id', $this->userid)->first() === null ? '已注销' :
            (new User())->where('id', $this->userid)->first()->user_name;
    }

    public function refUser(): \Illuminate\Database\Eloquent\Model|User|null
    {
        return (new User())->where('id', $this->ref_by)->first();
    }

    public function getRefUserNameAttribute(): string
    {
        return (new User())->where('id', $this->ref_by)->first() === null ? '已注销' :
            (new User())->where('id', $this->ref_by)->first()->user_name;
    }

    public function add(float $total, int $user_id, int $ref_by, float $ref_get, int $invoice_id): void
    {
        $this->total = $total;
        $this->userid = $user_id;
        $this->ref_by = $ref_by;
        $this->ref_get = $ref_get;
        $this->invoice_id = $invoice_id;
        $this->datetime = time();
        $this->save();
    }
}
