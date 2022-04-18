<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProductOrderNoIndex extends AbstractMigration
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
        $table = $this->table('product_order');
        $table->changeColumn('no', 'string', array('limit' => 255, 'comment' => '订单号'))
            ->save();

        $table->addIndex(array('no'), array('unique' => true, 'name' => 'order_no'))
            ->update();
    }
}
