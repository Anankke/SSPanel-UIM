<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LogTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('log');
        $table->addColumn('type', 'text', array('comment' => '类型分类', 'default' => 'uncategorized'))
            ->addColumn('reporter', 'text', array('comment' => '上报者', 'default' => 'anonymous'))
            ->addColumn('level', 'text', array('comment' => '日志等级', 'default' => 'low'))
            ->addColumn('msg', 'text', array('comment' => '消息正文'))
            ->addColumn('status', 'integer', array('comment' => '处理状态', 'default' => 0))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->create();
    }
}
