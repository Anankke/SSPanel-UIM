<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTwoFieldsToTheProductOrderTable extends AbstractMigration
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
        $table->addColumn('order_payment', 'text', array('comment' => '订单支付方式', 'after' => 'order_price'))
            ->addColumn('execute_status', 'integer', array('comment' => '订单处理状态', 'after' => 'paid_action'))
            ->update();
    }
}
