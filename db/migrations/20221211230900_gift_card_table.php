<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GiftCardTable extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->hasTable('gift_card')) {
            $this->table('gift_card', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
                ->addColumn('card', 'text', ['comment' => '卡号'])
                ->addColumn('balance', 'integer', ['comment' => '余额'])
                ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
                ->addColumn('status', 'integer', ['comment' => '使用状态'])
                ->addColumn('use_time', 'integer', ['comment' => '使用时间'])
                ->addColumn('use_user', 'integer', ['comment' => '使用用户'])
                ->addIndex([ 'id' ])
                ->addIndex([ 'status' ])
                ->create();
        }
    }

    public function down(): void
    {
        $this->table('gift_card')->drop()->update();
    }
}
