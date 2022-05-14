<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdjustConfigurationTableValueFieldLength extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('config');
        $table->changeColumn('value', 'string', ['limit' => 2048])
            ->save();
    }
}
