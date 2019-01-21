<?php


namespace App\Services\Token;

use App\Models\User;
use App\Models\Token as TokenModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DB extends Base
{
    public function store($tokenStr, User $user, $expireTime)
    {
        $token = new TokenModel();
        $token->token = $tokenStr;
        $token->user_id = $user->id;
        $token->create_time = time();
        $token->expire_time = $expireTime;
        if ($token->save()) {
            return true;
        }
        return false;
    }

    public function delete($token)
    {
        $token = TokenModel::where('token', $token)->first();
        if ($token == null) {
            return false;
        }
        $token->delete();
        return true;
    }

    public function get($token)
    {
        try {
            $tokenModel = TokenModel::where('token', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
        $token = new Token();
        $token->token = $tokenModel->token;
        $token->userId = $tokenModel->user_id;
        $token->createTime = $tokenModel->create_time;
        $token->expireTime = $tokenModel->expire_time;
        return $token;
    }
}
