<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserCoupon extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->hasTable('user_coupon')) {
            $this->table('user_coupon', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'integer', [ 'comment' => '优惠码ID', 'identity' => true ])
                ->addColumn('code', 'string', [ 'comment' => '优惠码' ])
                ->addColumn('content', 'json', [ 'comment' => '优惠码内容' ])
                ->addColumn('limit', 'json', [ 'comment' => '优惠码限制' ])
                ->addColumn('create_time', 'integer', [ 'comment' => '创建时间' ])
                ->addColumn('expire_time', 'integer', [ 'comment' => '过期时间' ])
                ->addIndex([ 'id' ])
                ->addIndex([ 'code' ])
                ->addIndex([ 'expire_time' ])
                ->create();
        }
    }

    public function down(): void
    {
        $this->table('user_coupon')->drop()->update();
    }
}
