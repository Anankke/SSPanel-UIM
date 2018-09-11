<?php


namespace App\Services\Auth;

use App\Services\Factory;

class Token extends Base
{
    protected $storage;

    public function __construct()
    {
        $this->storage = Factory::createTokenStorage();
    }

    public function login($uid, $time)
    {
        // TODO: Implement getUser() method.
    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }

    public function getUser()
    {
        $token = Cookie::get('token');
    }
}
