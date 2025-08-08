<?php

declare(strict_types=1);

/**
 * Create test user
 */
function createTestUser(array $attributes = []): \App\Models\User
{
    $faker = \Faker\Factory::create();
    
    $defaults = [
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->userName,
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'port' => rand(10000, 60000),
        'passwd' => bin2hex(random_bytes(16)),
        'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'method' => 'aes-256-gcm',
        'transfer_enable' => 1099511627776, // 1TB
        'u' => 0,
        'd' => 0,
        'money' => 0,
        'class' => 0,
        'node_group' => 0,
        'is_admin' => 0,
        'is_banned' => 0,
        'reg_date' => date('Y-m-d H:i:s'),
    ];
    
    $user = new \App\Models\User();
    foreach (array_merge($defaults, $attributes) as $key => $value) {
        $user->$key = $value;
    }
    
    return $user;
}

/**
 * Create test node
 */
function createTestNode(array $attributes = []): \App\Models\Node
{
    static $nodeId = 1;
    
    $defaults = [
        'id' => $nodeId++,
        'name' => 'Test Node ' . $nodeId,
        'type' => 'shadowsocks',
        'server' => 'node' . $nodeId . '.example.com',
        'sort' => 0,
        'info' => '1',
        'status' => 'online',
        'node_class' => 0,
        'node_group' => 0,
        'custom_config' => '{}',
    ];
    
    $node = new \App\Models\Node();
    foreach (array_merge($defaults, $attributes) as $key => $value) {
        $node->$key = $value;
    }
    
    return $node;
}

/**
 * Assert response is JSON format
 */
function assertJsonResponse($response): array
{
    $contentType = $response->getHeaderLine('Content-Type');
    PHPUnit\Framework\Assert::assertStringContainsString('application/json', $contentType);
    
    $body = (string) $response->getBody();
    $data = json_decode($body, true);
    PHPUnit\Framework\Assert::assertIsArray($data);
    
    return $data;
}

/**
 * Assert successful response
 */
function assertSuccessResponse($response): void
{
    PHPUnit\Framework\Assert::assertEquals(200, $response->getStatusCode());
    
    $data = assertJsonResponse($response);
    PHPUnit\Framework\Assert::assertEquals(1, $data['ret'] ?? $data['success'] ?? 0);
}

/**
 * Create test config
 */
function createTestConfig(array $items = []): void
{
    $defaults = [
        'appName' => 'SSPanel-Test',
        'version' => '2024.1',
        'db_version' => 2023020100,
        'baseUrl' => 'http://localhost',
        'muKey' => bin2hex(random_bytes(32)),
        'min_port' => 10000,
        'max_port' => 65535,
    ];
    
    foreach (array_merge($defaults, $items) as $key => $value) {
        \App\Models\Config::set($key, $value);
    }
}

/**
 * Clean test database
 */
function cleanTestDatabase(): void
{
    $db = \App\Services\DB::getPdo();
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        if ($table !== 'config' && $table !== 'migrations') {
            $db->exec("TRUNCATE TABLE `{$table}`");
        }
    }
    
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
}

/**
 * Run SQL file
 */
function runSqlFile(string $file): void
{
    $sql = file_get_contents($file);
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => !empty($s)
    );
    
    $db = \App\Services\DB::getPdo();
    foreach ($statements as $statement) {
        $db->exec($statement);
    }
}