<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\User;

class UserFactory
{
    protected array $attributes = [];
    
    public function create(array $attributes = []): User
    {
        $data = array_merge($this->getDefaults(), $this->attributes, $attributes);
        
        $user = new User();
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
        $user->save();
        
        return $user;
    }
    
    public function make(array $attributes = []): User
    {
        $data = array_merge($this->getDefaults(), $this->attributes, $attributes);
        
        $user = new User();
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
        
        return $user;
    }
    
    public function count(int $count): self
    {
        $factory = clone $this;
        $factory->count = $count;
        return $factory;
    }
    
    public function state(array $attributes): self
    {
        $factory = clone $this;
        $factory->attributes = array_merge($factory->attributes, $attributes);
        return $factory;
    }
    
    protected function getDefaults(): array
    {
        $faker = \Faker\Factory::create();
        
        return [
            'email' => $faker->unique()->safeEmail,
            'username' => $faker->userName,
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'money' => 0,
            'is_admin' => 0,
            'is_banned' => 0,
            'is_shadow_banned' => 0,
            'theme' => 'tabler',
            'locale' => 'zh-CN',
            'reg_date' => date('Y-m-d H:i:s'),
            'method' => 'aes-256-gcm',
            'port' => rand(10000, 60000),
            'passwd' => bin2hex(random_bytes(16)),
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'transfer_enable' => 1099511627776, // 1TB
            'u' => 0,
            'd' => 0,
            'node_iplimit' => 0,
            'node_speedlimit' => 0,
            'node_group' => 0,
            'class' => 0,
            'class_expire' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'ga_token' => '',
            'ga_enable' => 0,
        ];
    }
    
    /**
     * Create admin user
     */
    public static function admin(): self
    {
        return (new self())->state(['is_admin' => 1]);
    }
    
    /**
     * Create banned user
     */
    public static function banned(): self
    {
        return (new self())->state(['is_banned' => 1]);
    }
    
    /**
     * Create VIP user
     */
    public static function vip(): self
    {
        return (new self())->state([
            'class' => 1,
            'class_expire' => date('Y-m-d H:i:s', strtotime('+1 year')),
            'node_group' => 1,
        ]);
    }
}