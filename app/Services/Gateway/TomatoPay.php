<?php
namespace App\Services\Gateway;
use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;
class TomatoPay extends AbstractPayment
{

    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if($price <= 0){
            return json_encode(['errcode'=>-1,'errmsg'=>"非法的金额."]);
        }
        $user = Auth::getUser();
        $settings = Config::get("tomatopay")[$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        $fqaccount=$settings['account'];
        $fqkey=$settings['token'];
        $fqmchid = $settings['mchid'];
		$fqtype = 1;
		$fqtrade = $pl->tradeno;
	    $fqcny = $price;
        $data = [
            'account' => $settings['account'],
			'mchid' => $settings['mchid'],
			'type' => 1,
			'trade' => $pl->tradeno,
			'cny' => $price,
        ];
      $signs = md5("mchid=".$fqmchid."&account=".$fqaccount."&cny=".$fqcny."&type=1&trade=".$fqtrade.$fqkey);
        $url="https://b.fanqieui.com/gateways/".$type.".php?account=".$fqaccount."&mchid=".$fqmchid."&type=".$fqtype."&trade=".$fqtrade."&cny=".$fqcny."&signs=".$signs;
		$result = "<script language='javascript' type='text/javascript'>window.location.href='".$url."';</script>";
        $result = json_encode(array('code'=>$result,'errcode'=>0,'pid' =>$pl->id));
        return $result;
    }
    
    public function notify($request, $response, $args)
    {
		$type = $args['type'];
		      $settings = Config::get("tomatopay")[$type];
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
           $this->postPayment($order_data['out_trade_no'], "在线支付");
        echo 'success';
        if($ispost==0) header("Location: /user/code");
			

        }else{
           echo '验证失败';
        }
    }
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
