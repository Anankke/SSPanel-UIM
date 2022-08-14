<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropGConfigTable extends AbstractMigration
{
    public function up(): void
    {
        $this->table('gconfig')->drop()->update();
    }

    public function down(): void
    {
        $table = $this->table('gconfig');
        $this->table('gconfig', [ 'id' => false, 'primary_key' => [ 'id' ]])
            ->addColumn('id', 'integer', [ 'identity' => true,'signed' => false ])
            ->addColumn('key', 'string', [ 'comment' => '配置键名' ])
            ->addColumn('type', 'string', [ 'comment' => '值类型' ])
            ->addColumn('value', 'string', [ 'comment' => '配置值' ])
            ->addColumn('oldvalue', 'string', [ 'comment' => '之前的配置值' ])
            ->addColumn('name', 'string', [ 'comment' => '配置名称' ])
            ->addColumn('comment', 'string', [ 'comment' => '配置描述' ])
            ->addColumn('operator_id', 'integer', [ 'comment' => '操作员 ID' ])
            ->addColumn('operator_name', 'string', [ 'comment' => '操作员名称' ])
            ->addColumn('operator_email', 'string', [ 'comment' => '操作员邮箱' ])
            ->addColumn('last_update', 'biginteger', [ 'comment' => '修改时间' ])
            ->create();
    }
}
