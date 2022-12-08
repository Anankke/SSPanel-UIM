<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserApiToken extends AbstractMigration
{
    public function up(): void
    {
        if (! $this->table('user')->hasColumn('api_token')) {
            $this->table('user')
                ->addColumn('api_token', 'uuid', [ 'comment' => 'API 密钥', 'null' => false, 'default' => '' ])
                ->save();
        }
    }

    public function down(): void
    {
        $this->table('user')
            ->removeColumn('api_token')
            ->save();
    }
}
