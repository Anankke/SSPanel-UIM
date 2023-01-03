<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProductOrderInvoice extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->hasTable('product')) {
            $this->table('product', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'integer', [ 'comment' => '商品ID', 'identity' => true ])
                ->addColumn('type', 'string', [ 'comment' => '类型' ])
                ->addColumn('name', 'string', [ 'comment' => '名称' ])
                ->addColumn('price', 'double', [ 'comment' => '售价' ])
                ->addColumn('content', 'json', [ 'comment' => '内容' ])
                ->addColumn('limit', 'json', [ 'comment' => '购买限制'])
                ->addColumn('status', 'integer', [ 'comment' => '销售状态' ])
                ->addColumn('create_time', 'integer', [ 'comment' => '创建时间' ])
                ->addColumn('update_time', 'integer', [ 'comment' => '更新时间' ])
                ->addColumn('sale_count', 'integer', [ 'comment' => '累计销售数'])
                ->addColumn('stock', 'integer', [ 'comment' => '库存'])
                ->addIndex([ 'id' ])
                ->addIndex([ 'type' ])
                ->create();
        }

        if (! $this->hasTable('order')) {
            $this->table('order', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'integer', [ 'comment' => '订单ID', 'identity' => true ])
                ->addColumn('user_id', 'integer', [ 'comment' => '提交用户' ])
                ->addColumn('product_id', 'integer', [ 'comment' => '商品ID' ])
                ->addColumn('product_type', 'string', [ 'comment' => '商品类型' ])
                ->addColumn('product_name', 'string', [ 'comment' => '商品名称' ])
                ->addColumn('product_content', 'json', [ 'comment' => '商品内容' ])
                ->addColumn('coupon', 'string', [ 'comment' => '订单优惠码'])
                ->addColumn('price', 'double', [ 'comment' => '订单金额' ])
                ->addColumn('status', 'string', [ 'comment' => '订单状态' ])
                ->addColumn('create_time', 'integer', [ 'comment' => '创建时间' ])
                ->addColumn('update_time', 'integer', [ 'comment' => '更新时间' ])
                ->addIndex([ 'id' ])
                ->addIndex([ 'user_id' ])
                ->addIndex([ 'product_id' ])
                ->addIndex([ 'status' ])
                ->create();
        }

        if (! $this->hasTable('invoice')) {
            $this->table('invoice', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'integer', [ 'comment' => '账单ID', 'identity' => true ])
                ->addColumn('user_id', 'integer', [ 'comment' => '归属用户' ])
                ->addColumn('order_id', 'integer', [ 'comment' => '订单ID' ])
                ->addColumn('content', 'json', [ 'comment' => '账单内容' ])
                ->addColumn('price', 'double', [ 'comment' => '账单金额' ])
                ->addColumn('status', 'string', [ 'comment' => '账单状态' ])
                ->addColumn('create_time', 'integer', [ 'comment' => '创建时间' ])
                ->addColumn('update_time', 'integer', [ 'comment' => '更新时间' ])
                ->addColumn('pay_time', 'integer', [ 'comment' => '支付时间' ])
                ->addIndex([ 'id' ])
                ->addIndex([ 'user_id' ])
                ->addIndex([ 'order_id' ])
                ->addIndex([ 'status' ])
                ->create();
        }
    }

    public function down(): void
    {
        $this->table('product')->drop()->update();
        $this->table('order')->drop()->update();
        $this->table('invoice')->drop()->update();
    }
}
