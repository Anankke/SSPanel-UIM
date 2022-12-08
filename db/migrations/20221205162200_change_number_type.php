<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ChangeNumberType extends AbstractMigration
{
    public function up(): void
    {
        $this->table('user')
            ->changeColumn('port', 'smallinteger', [ 'comment' => '端口', 'null' => false ])
            ->changeColumn('class', 'integer', [ 'comment' => '等级', 'default' => 0, 'signed' => false, 'null' => false ])
            ->changeColumn('node_group', 'integer', [ 'comment' => '节点分组', 'default' => 0, 'signed' => false, 'null' => false ])
            ->changeColumn('node_speedlimit', 'double', [ 'comment' => '用户限速', 'default' => 0, 'null' => false ])
            ->changeColumn('node_iplimit', 'smallinteger', [ 'comment' => '同时可连接IP数', 'default' => 0, 'null' => false ])
            ->changeColumn('uuid', 'uuid', [ 'comment' => 'UUID', 'null' => false ])
            ->save();
        $this->table('node')
            ->changeColumn('node_speedlimit', 'double', [ 'comment' => '节点限速', 'null' => false ])
            ->save();
    }

    public function down(): void
    {
        $this->table('user')
            ->changeColumn('port', 'integer', [ 'comment' => '用户端口' ])
            ->changeColumn('class', 'integer', [ 'comment' => '用户等级', 'default' => 0 ])
            ->changeColumn('node_group', 'integer', [ 'comment' => '节点分组', 'default' => 0 ])
            ->changeColumn('node_speedlimit', 'decimal', [ 'comment' => '每个连接限速', 'default' => 0 ])
            ->changeColumn('uuid', 'string', [ 'comment' => 'UUID' ])
            ->save();
        $this->table('node')
            ->changeColumn('node_speedlimit', 'decimal', [ 'default' => 0.00,'precision' => 12, 'scale' => 2 ])
            ->save();
    }
}
