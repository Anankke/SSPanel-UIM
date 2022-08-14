<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FixUserMoneyField extends AbstractMigration
{
    public function change(): void
    {
        // https://github.com/lucasjkr/opencommerce/blob/2a7c6851a61b5078842c6353393b7fea2b818b09/db/migrations/20171111111123_change_price_precision.php

        $table = $this->table('user');
        $table->changeColumn('money', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false, 'default' => '0.00'])
            ->save();
    }
}
