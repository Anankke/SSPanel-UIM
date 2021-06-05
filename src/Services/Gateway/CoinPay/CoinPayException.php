<?php

namespace App\Services\Gateway\CoinPay;

class CoinPayException extends \Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
