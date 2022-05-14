<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStreamMediaTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('stream_media');
        $table->addColumn('node_id', 'integer', ['comment' => '节点id'])
            ->addColumn('result', 'text', ['comment' => '检测结果'])
            ->addColumn('created_at', 'integer', ['comment' => '创建时间'])
            ->create();
    }

    public function down(): void
    {
        $this->table('stream_media')->drop()->update();
    }
}
