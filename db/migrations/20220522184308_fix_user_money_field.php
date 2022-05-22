<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FixUserMoneyField extends AbstractMigration
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
        // https://github.com/lucasjkr/opencommerce/blob/2a7c6851a61b5078842c6353393b7fea2b818b09/db/migrations/20171111111123_change_price_precision.php

        $table = $this->table('user');
        $table->changeColumn('money', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false, 'default' => '0.00'])
            ->save();
    }
}
