<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDocs extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->hasTable('docs')) {
            $this->table('docs', [ 'id' => false, 'primary_key' => [ 'id' ]])
                ->addColumn('id', 'biginteger', [ 'identity' => true,'signed' => false ])
                ->addColumn('date', 'datetime', [])
                ->addColumn('title', 'string', [])
                ->addColumn('content', 'string', [])
                ->addColumn('markdown', 'string', [])
                ->create();
        }
    }

    public function down(): void
    {
        $this->table('docs')->drop()->update();
    }
}
