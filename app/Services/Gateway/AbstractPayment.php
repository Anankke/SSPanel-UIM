<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午4:23
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Payback;
use App\Models\User;
use App\Models\Code;
use App\Services\Config;


abstract class AbstractPayment
{
    public $method;
    abstract protected function init();
    abstract protected function setMethod($method);
    abstract protected function setNotifyUrl();
    abstract protected function setReturnUrl();
    abstract protected function purchase($request, $response, $args);
    abstract protected function notify($request, $response, $args);
    abstract protected function sign();
    abstract protected function getPurchaseHTML();
    abstract protected function getReturnHTML($request, $response, $args);
    abstract protected function getStatus($request, $response, $args);

    public function postPayment($pid, $method){
        $p=Paylist::find($pid);
        if($p->status==1){
            return json_encode(['errcode'=>0]);
        }
        $p->status=1;
        $p->save();
        $user = User::find($p->userid);
        $user->money += $p->total;
        $user->save();
        $codeq=new Code();
        $codeq->code=$method;
        $codeq->isused=1;
        $codeq->type=-1;
        $codeq->number=$p->total;
        $codeq->usedatetime=date("Y-m-d H:i:s");
        $codeq->userid=$user->id;
        $codeq->save();

        if ($user->ref_by!="" && $user->ref_by!=0 && $user->ref_by!=null) {
            $gift_user=User::where("id", "=", $user->ref_by)->first();
            $gift_user->money=($gift_user->money+($codeq->number*(Config::get('code_payback')/100)));
            $gift_user->save();
            $Payback=new Payback();
            $Payback->total=$codeq->number;
            $Payback->userid=$user->id;
            $Payback->ref_by=$user->ref_by;
            $Payback->ref_get=$codeq->number*(Config::get('code_payback')/100);
            $Payback->datetime=time();
            $Payback->save();
        }

        if (Config::get('enable_donate') == 'true') {
            if ($user->is_hide == 1) {
                Telegram::Send("一位不愿透露姓名的大老爷给我们捐了 ".$codeq->number." 元!");
            } else {
                Telegram::Send($user->user_name." 大老爷给我们捐了 ".$codeq->number." 元！");
            }
        }
        return 0;

    }

}