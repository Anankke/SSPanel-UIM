<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    private const UP = <<< END
        // Your upgrade SQL here 
END;

    private const DOWN = <<< END
        // Your downgrade SQL here(optional)
END;

    public function up(): void
    {
        DB::getPdo()->exec(self::UP);
    }

    public function down(): void
    {
        DB::getPdo()->exec(self::DOWN);
    }
};
