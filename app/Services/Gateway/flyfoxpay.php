<?php
namespace App\Services\Gateway;
use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;
class flyfoxpay extends AbstractPayment
{

    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if($price <= 0){
            return json_encode(['errcode'=>-1,'errmsg'=>"非法的金额."]);
        }
        $user = Auth::getUser();
        $settings = Config::get("flyfoxpay")['config'];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
      if($type=='wxpay'){$typesss='o_wxpay';}elseif($type=='alipay'){$typesss='o_alipay';}
        $ffaccount=$settings['mail'];
        $ffkey=md5($settings['key']);
        $ffmchid = $settings['hid'];
		$fftype = 'all';
		$fftrade = $pl->tradeno;
	    $ffcny = $price/0.056;
        $data = [
            'mail' => $settings['mail'],
			'hid' => $settings['hid'],
			'type' => 'all',
			'trade' => $pl->tradeno,
			'cny' => $price,
        ];
        $return='https://'.$_SERVER['HTTP_HOST'].'/flyfoxpay_back/'.$type;
        $url="https://sc-i.pw/api/?mail=".$ffaccount."&id=".$ffmchid."&keys=".$ffkey."&type=".$typesss."&trade_no=".time()."&amount=".$ffcny."&trade_name=".time()."&return=".$return."&go=1&customize1=".$pl->tradeno;
		$result = "<script language='javascript' type='text/javascript'>window.location.href='".$url."';</script>";
        $result = json_encode(array('code'=>$result,'errcode'=>0,'pid' =>$pl->id));
        return $result;
    }
    
    public function notify($request, $response, $args)
    {
		$type = $args['type'];
		$settings = Config::get("flyfoxpay")['config'];
        $security['orderid'] = $_REQUEST['orderid'];
      if($security['orderid']=='' OR $security['orderid']==null){header("Location: /user/code");}else{
//手续费
$fee = 0;
$url = "https://sc-i.pw/api/check/";//API位置
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
 array("key"=>$settings["key"], //商家KEY
       "id"=>$settings['hid'], //商家ID
       "mail"=>$settings['mail'], //商家EMAIL
       "trade_no"=>$security['orderid'], //商家訂單ID
       ))); 
$output = curl_exec($ch); 
curl_close($ch);
/*
回傳格式:
//成功
{"status":"200","status_trade":"noapy","sign":"90e5f1f7ef87cd2e43729ba4378656b5"}
{"status":"200","trade_no":"1278217527512","type":"o_alipay","status_trade":"payok","sign":"*****"}
//以下為錯誤項目
{"status":"404","error":"未設置KEY或是ID或MAIL"}
{"status":"400","error":"請檢查ID或是KEY或MAIL是否有誤"}
{"status":"416","error":"請檢查訂單ID是否有誤"}
*/ 
$security1  = array();

$security1['mchid']      = $settings['hid'];//商家ID

$security1['status']        = "7";//驗證，請勿更改

$security1['mail']      = $settings['mail'];//商家EMAIL

$security1['trade_no']      = $security['orderid'];//商家訂單ID

foreach ($security1 as $k=>$v)

{

    $o.= "$k=".($v)."&";

}

$sign1 = md5(substr($o,0,-1).$settings["key"]);//**********請替換成商家KEY
$json=json_decode($output, true);
if($json['sign']==$sign1){
  if($json['status_trade']=="payok"){
           $this->postPayment($json['customize1'], "在线支付");
        echo 'success';
       header("Location: /user/code");

        }elseif($json['status_trade']=="payokbaterror"){
           echo '验证失败，payokbaterror';
        }}else{
           echo '验证失败';
        }
    }}
    public function getPurchaseHTML()
    {
        return '
                    <div class="card-inner">
										<p class="card-heading">充值</p>
										<h5>支付方式:</h5>
										<nav class="tab-nav margin-top-no">
											<ul class="nav nav-list">
											
													<li>
														<a class="waves-attach waves-effect type active" data-toggle="tab" data-pay="wxpay">微信支付</a>
													</li>
											
								
													<li>
														<a class="waves-attach waves-effect type" data-toggle="tab" data-pay="alipay">支付宝</a>
													</li>
											
				
											</ul>
											<div class="tab-nav-indicator"></div>
										</nav>
										<div class="form-group form-group-label">
											<label class="floating-label" for="amount">金额</label>
											<input class="form-control" id="amount" type="text">
										</div>
									</div>
                                    <div class="card-action">
										<div class="card-action-btn pull-left">
											<button class="btn btn-flat waves-attach" id="code-update" ><span class="icon">check</span>&nbsp;充值</NOtton>
										</div>
									</div>
                        <script>
		var type = "wxpay";
			var type = "alipay";
	var pid = 0;
	$(".type").click(function(){
		type = $(this).data("pay");
	});
	$("#code-update").click(function(){
		var price = parseFloat($("#amount").val());
		console.log("将要使用"+type+"方法充值"+price+"元")
		if(isNaN(price)){
			$("#result").modal();
			$("#msg").html("非法的金额!");
		}
		$.ajax({
			\'url\':"/user/payment/purchase",
			\'data\':{
				\'price\':price,
				\'type\':type,
			},
			\'dataType\':\'json\',
			\'type\':"POST",
			success:function(data){
				console.log(data);
				if(data.errcode==-1){
					$("#result").modal();
					$("#msg").html(data.errmsg);
				}
				if(data.errcode==0){
					pid = data.pid;
					if(type=="wxpay"){
						$("#result").modal();
						$("#msg").html("正在跳转到微信..."+data.code);
					}else if(type=="alipay"){
						$("#result").modal();
						$("#msg").html("正在跳转到支付宝..."+data.code);
					}
				}
			}
		});
		setTimeout(f, 1000);
	});
</script>
';
    }
    public function getReturnHTML($request, $response, $args)
    {

    }
    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }
}
