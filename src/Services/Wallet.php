<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserMoneyLog;
use LogicException;
use function bcadd;
use function bccomp;
use function bcround;
use function bcsub;
use function is_string;
use function sprintf;

/**
 * 用户余额的写入口：调用方只传意图，这里锁住用户行后在同一事务里改余额并写一条余额记录，已有调用方事务时并入其中
 */
final class Wallet
{
    public static function credit(int $userId, string|int|float $amount, string $remark): string
    {
        return self::apply($userId, self::normalize($amount), $remark);
    }

    /**
     * 扣款金额由调用方在锁内按当前余额算好，余额不足属于调用方错误
     */
    public static function debit(int $userId, string|int|float $amount, string $remark): string
    {
        return self::apply($userId, bcsub('0', self::normalize($amount), 2), $remark);
    }

    private static function normalize(string|int|float $amount): string
    {
        if (is_string($amount)) {
            return bcround($amount, 2);
        }

        return bcround(sprintf('%.10F', $amount), 2);
    }

    private static function apply(int $userId, string $delta, string $remark): string
    {
        $connection = DB::connection();

        if ($connection->transactionLevel() === 0) {
            return $connection->transaction(
                static fn (): string => self::write($userId, $delta, $remark),
                3
            );
        }

        return self::write($userId, $delta, $remark);
    }

    private static function write(int $userId, string $delta, string $remark): string
    {
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
