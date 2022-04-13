<?php
declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class ProductOrderTable extends AbstractMigration
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
        $table->addColumn('no', 'text', array('comment' => '订单号'))
            ->addColumn('user_id', 'integer', array('comment' => '提交用户'))
            ->addColumn('product_id', 'integer', array('comment' => '订单商品'))
            ->addColumn('product_name', 'text', array('comment' => '商品名称'))
            ->addColumn('product_type', 'text', array('comment' => '商品类型'))
            ->addColumn('product_content', 'text', array('comment' => '商品内容'))
            ->addColumn('product_price', 'integer', array('comment' => '商品售价'))
            ->addColumn('order_coupon', 'text', array('comment' => '订单优惠码', 'default' => null, 'null' => true))
            ->addColumn('order_price', 'integer', array('comment' => '订单金额'))
            ->addColumn('order_status', 'text', array('comment' => '订单状态'))
            ->addColumn('created_at', 'integer', array('comment' => '创建时间'))
            ->addColumn('updated_at', 'integer', array('comment' => '更新时间'))
            ->addColumn('expired_at', 'integer', array('comment' => '过期时间'))
            ->addColumn('paid_at', 'integer', array('comment' => '支付时间'))
            ->addColumn('paid_action', 'text', array('comment' => '支付后操作'))
            ->create();
    }
}
