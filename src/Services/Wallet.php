<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserMoneyLog;
use LogicException;
use function bcadd;
use function bccomp;
use function bcsub;

/**
 * 用户余额的写入口：调用方须已开启事务，这里锁住用户行后改余额并写一条余额记录
 */
final class Wallet
{
    public static function credit(int $userId, string $amount, string $remark): string
    {
        return self::apply($userId, $amount, $remark);
    }

    /**
     * 扣款金额由调用方在锁内按当前余额算好，余额不足属于调用方错误
     */
    public static function debit(int $userId, string $amount, string $remark): string
    {
        return self::apply($userId, bcsub('0', $amount, 2), $remark);
    }

    private static function apply(int $userId, string $delta, string $remark): string
    {
        if (DB::connection()->transactionLevel() === 0) {
            throw new LogicException('Wallet must be called inside a database transaction');
        }

        $user = (new User())->lockForUpdate()->findOrFail($userId);
        $before = (string) $user->money;
        $after = bcadd($before, $delta, 2);

        if (bccomp($after, '0', 2) < 0) {
            throw new LogicException('Insufficient balance for user #' . $userId);
        }

        $user->money = $after;
        $user->save();

        (new UserMoneyLog())->add($userId, (float) $before, (float) $after, (float) $delta, $remark);

        return $after;
    }
}
