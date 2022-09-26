<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTrafficLog extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('transfer_total')) {
            $this->table('user')
                ->addColumn('transfer_total', 'biginteger', [ 'comment' => '账户累计使用流量', 'default' => 0, 'signed' => false])
                ->save();
        }

        if (! $this->hasTable('user_hourly_usage')) {
            $this->table('user_hourly_usage', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
                ->addColumn('user_id', 'biginteger', [ 'signed' => false ])
                ->addColumn('traffic', 'biginteger', [])
                ->addColumn('hourly_usage', 'biginteger', [])
                ->addColumn('datetime', 'integer', [])
                ->addIndex([ 'user_id' ])
                ->addForeignKey('user_id', 'user', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
                ->create();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->removeColumn('transfer_total')
            ->save();
        $this->table('user_hourly_usage')->drop()->update();
    }
}
