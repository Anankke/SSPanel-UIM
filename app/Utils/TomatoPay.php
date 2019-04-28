<?php
namespace App\Utils;
/**
 * Made By Leo
 * 番茄云支付接口
 */

use App\Services\View;
use App\Services\Auth;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\InviteCode;
use App\Models\CheckInLog;
use App\Models\Ann;
use App\Models\Speedtest;
use App\Models\Shop;
use App\Models\Coupon;
use App\Models\Bought;
use App\Models\Ticket;
use App\Services\Config;
use App\Utils\Hash;
use App\Utils\Tools;
use App\Utils\Radius;
use App\Utils\Wecenter;
use App\Models\RadiusBan;
use App\Models\DetectLog;
use App\Models\DetectRule;
use voku\helper\AntiXSS;
use App\Models\User;
use App\Models\Code;
use App\Models\Ip;
use App\Models\Paylist;
use App\Models\LoginIp;
use App\Models\BlockIp;
use App\Models\UnblockIp;
use App\Models\Payback;
use App\Models\Relay;
use App\Utils\QQWry;
use App\Utils\GA;
use App\Utils\Geetest;
use App\Utils\Telegram;
use App\Utils\TelegramSessionManager;
use App\Utils\Pay;
use App\Utils\URL;
use App\Services\Mail;

class TomatoPay{

    protected $enabled = [
        'wxpay'=>1, // 1 启用 0 关闭
        'submit'=>1, // 1 启用 0 关闭
        'qqpay'=>1, // 1 启用 0 关闭
        ];

    protected $data = [
        'wxpay'=>[
            'mchid' => 1555860947,   // 商户号
            'account' => '2487642542@qq.com', //登陆邮箱
            'token' => "qGNbcGjW8MFhDupjxeJy7wqDUBoz7ZJg" // 安全验证码
        ],
        'submit'=>[
            'mchid' => 1555860935,   // 商户号
            'account' => '2487642542@qq.com', //登陆邮箱
            'token' => "owVtOoA7n7e3MM7J4yJxiKMaQ8NEOJjr" // 安全验证码
        ],
        'qqpay'=>[
            'mchid' => 1511606539,   // 商户号
            'account' => 'admin@fanqieui.com', //登陆邮箱
            'token' => "yKgdXms8n4HS8DGW5YmItOxSwzsw3lmz" // 安全验证码
        ],
    ];

    public function smarty()
    {
        $this->smarty = View::getSmarty();
        return $this->smarty;
    }

    public function view()
    {
        return $this->smarty();
    }

    public function route_home($request, $response, $args){
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $codes = Code::where('type', '<>', '-2')->where('userid', '=', Auth::getUser()->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/code');
        return $this->view()->assign('codes', $codes)->assign('enabled',$this->enabled)->display('user/tomatopay.tpl');
    }
    public function handel($request, $response, $args){
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if($this->enabled[$type]==0){
            return json_encode(['errcode'=>-1,'errmsg'=>"非法的支付方式."]);
        }
        if($price <= 0){
            return json_encode(['errcode'=>-1,'errmsg'=>"非法的金额."]);
        }
        $user = Auth::getUser();
        $settings = $this->data[$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->save();
        $fqaccount=$settings['account'];
        $fqkey=$settings['token'];
        $fqmchid = $settings['mchid'];
		$fqtype = 1;
		$fqtrade = $pl->id;
	    $fqcny = $price;
        $data = [
            'account' => $settings['account'],
			'mchid' => $settings['mchid'],
			'type' => 1,
			'trade' => $pl->id,
			'cny' => $price,
        ];
      $signs = md5("mchid=".$fqmchid."&account=".$fqaccount."&cny=".$fqcny."&type=1&trade=".$fqtrade.$fqkey);
        $url="https://b.fanqieui.com/gateways/".$type.".php?account=".$fqaccount."&mchid=".$fqmchid."&type=".$fqtype."&trade=".$fqtrade."&cny=".$fqcny."&signs=".$signs;
		$result = "<script language='javascript' type='text/javascript'>window.location.href='".$url."';</script>";
        $result = json_encode(array('code'=>$result,'errcode'=>0,'pid' =>$pl->id));
        return $result;
    }
    public function status($request, $response, $args){
        return json_encode(Paylist::find($_POST['pid']));
    }
    public function handel_return($request, $response, $args){
       $money = $_GET['money'];
         echo "您已经成功支付 $money 元,正在跳转..";
         echo <<<HTML
<script>
    location.href="/user/tomatopay";
</script>
HTML;
        return;
    }
    public function handel_wxcallback($request, $response, $args){
       $type = wxpay;
      $settings = $this->data[$type];
                $order_data = $_REQUEST;
        $transid   = $order_data['trade_no'];       //转账交易号
		$invoiceid = $order_data['out_trade_no'];     //订单号
		$amount    = $order_data['total_fee'];          //获取递过来的总价格
		$status    = $order_data['trade_status'];         //获取传递过来的交易状态
      $signs    = $order_data['sign']; 
      
      $security  = array();
      $security['out_trade_no']      = $invoiceid;
      $security['total_fee']    = $amount;
      $security['trade_no']        = $transid;
      $security['trade_status']       = $status;
foreach ($security as $k=>$v)
{
    $o.= "$k=".urlencode($v)."&";
}
$sign = md5(substr($o,0,-1).$settings['token']);
      
        if ($sign == $signs) {
            $p=Paylist::find($invoiceid);
            $p->status=1;
            $p->save();
            $user = User::find($p->userid);
            $user->money += $p->total;
          $user->save();
          //更新充值（捐赠）记录
          $codeq=new Code();
            $codeq->code="微信充值";
            $codeq->isused=1;
            $codeq->type=-1;
            $codeq->number=$amount;
            $codeq->usedatetime=date("Y-m-d H:i:s");
            $codeq->userid=$user->id;
                      $codeq->save();
            return json_encode(['errcode'=>0]);
			

        }else{
           echo '验证失败';
        }
    }
  public function handel_alicallback($request, $response, $args){
       $type = submit;
      $settings = $this->data[$type];
                $order_data = $_REQUEST;
        $transid   = $order_data['trade_no'];       //转账交易号
		$invoiceid = $order_data['out_trade_no'];     //订单号
		$amount    = $order_data['total_fee'];          //获取递过来的总价格
		$status    = $order_data['trade_status'];         //获取传递过来的交易状态
      $signs    = $order_data['sign']; 
      
      $security  = array();
      $security['out_trade_no']      = $invoiceid;
      $security['total_fee']    = $amount;
      $security['trade_no']        = $transid;
      $security['trade_status']       = $status;
foreach ($security as $k=>$v)
{
    $o.= "$k=".urlencode($v)."&";
}
$sign = md5(substr($o,0,-1).$settings['token']);
      
        if ($sign == $signs) {
            $p=Paylist::find($invoiceid);
            $p->status=1;
            $p->save();
            $user = User::find($p->userid);
            $user->money += $p->total;
          $user->save();
          //更新充值（捐赠）记录
          $codeq=new Code();
            $codeq->code="支付宝充值";
            $codeq->isused=1;
            $codeq->type=-1;
            $codeq->number=$amount;
            $codeq->usedatetime=date("Y-m-d H:i:s");
            $codeq->userid=$user->id;
                      $codeq->save();
            return json_encode(['errcode'=>0]);
    }
}}
