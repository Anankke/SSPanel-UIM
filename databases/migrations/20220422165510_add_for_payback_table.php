<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddForPaybackTable extends AbstractMigration
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
        $table = $this->table('payback');
        $table->addColumn('associated_order', 'string', array('comment' => '关联订单', 'after' => 'ref_get', 'default' => null, 'null' => true))
            ->addColumn('fraud_detect', 'string', array('comment' => '是否欺诈', 'after' => 'ref_get', 'default' => 0))
            ->update();
    }
}
