<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class StatisticsTable extends AbstractMigration
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
        $table = $this->table('statistics');
        $table->addColumn('item', 'text', array('comment' => '记录项目'))
            ->addColumn('value', 'text', array('comment' => '记录值'))
            ->addColumn('user_id', 'integer', array('comment' => '关联用户', 'default' => null, 'null' => true))
            ->addColumn('node_id', 'integer', array('comment' => '关联节点', 'default' => null, 'null' => true))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->create();
    }
}
