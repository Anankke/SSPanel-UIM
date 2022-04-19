<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NodeRemark extends AbstractMigration
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
        $table = $this->table('node');
        $table->addColumn('remark', 'text', array('comment' => '管理员备注', 'after' => 'status', 'default' => '无'))
            ->update();
    }
}
