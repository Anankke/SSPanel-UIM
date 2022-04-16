<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class WorkOrderTable extends AbstractMigration
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
        $table = $this->table('work_order');
        $table->addColumn('tk_id', 'integer', array('comment' => '围绕主题'))
            ->addColumn('is_topic', 'integer', array('comment' => '是否是主题帖'))
            ->addColumn('title', 'text', array('comment' => '主题帖标题', 'default' => null, 'null' => true))
            ->addColumn('content', 'text', array('comment' => '围绕主题帖的回复内容'))
            ->addColumn('user_id', 'integer', array('comment' => '提交用户'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->addColumn('updated_at', 'integer', array('comment' => '更新时间'))
            ->addColumn('closed_at', 'integer', array('comment' => '关闭时间', 'default' => null, 'null' => true))
            ->addColumn('closed_by', 'text', array('comment' => '关闭人', 'default' => null, 'null' => true))
            ->create();
    }
}
