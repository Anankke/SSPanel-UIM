<?php

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;

use App\Models\Paylist;




class TomatoPay extends AbstractPayment
{
    public function getPurchaseHTML()
    {
        echo'<script>
    location.href="/user/tomatopay";
</script>';
    }
   public function purchase($request, $response, $args)
   {}
  public function notify($request, $response, $args)
   {}
   public function getStatus($request, $response, $args)
   {}
   public function getReturnHTML($request, $response, $args)
   {}



}

