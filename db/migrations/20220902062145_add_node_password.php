<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNodePassword extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('node')->hasColumn('password')) {
            $this->table('node')
                ->addColumn('password', 'string')
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('node')
            ->removeColumn('password')
            ->save();
    }
}
