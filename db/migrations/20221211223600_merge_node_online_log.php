<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MergeNodeOnlineLog extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('node_online_log')) {
            $this->table('node_online_log')->drop()->update();
        }
        if (! $this->table('node')->hasColumn('online_user')) {
            $this->table('node')
                ->addColumn('online_user', 'integer', [ 'comment' => '节点在线用户', 'default' => 0 ])
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('node_online_log', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true ])
            ->addColumn('node_id', 'integer', [])
            ->addColumn('online_user', 'integer', [])
            ->addColumn('log_time', 'integer', [])
            ->addIndex([ 'node_id' ])
            ->addForeignKey('node_id', 'node', 'id', [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
            ->create();
        $this->table('node')
            ->removeColumn('online_user')
            ->save();
    }
}
