<div class="card-inner">
	<p class="card-heading">Recharge</p>
	Current balance:{$user->money}
	<h5>Payment method:</h5>
	<nav class="tab-nav margin-top-no">
		<ul class="nav nav-list">
			{if $enabled['wepay']}
				<li class="active">
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="wepay">Wechat Pay</a>
				</li>
			{/if}
			{if $enabled['alipay']}
				<li>
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="alipay">Alipay</a>
				</li>
			{/if}
			{if $enabled['qqpay']}
				<li>
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="qqpay">QQ Pay</a>
				</li>
			{/if}
		</ul>
		<div class="tab-nav-indicator"></div>
	</nav>
	<div class="form-group form-group-label">
		<label class="floating-label" for="amount">Amount</label>
		<input class="form-control" id="amount" type="text">
	</div>
</div>
<div class="card-action">
	<div class="card-action-btn pull-left">
		<button class="btn btn-flat waves-attach" id="submit" ><span class="icon">check</span>&nbsp;Recharge</button>
	</div>
</div>
<script>
	var type = "wepay";
	var pid = 0;
window.onload = function() {
    var qrcode = new QRCode(document.getElementById("dmy"));
	$(".type").click(function(){
		type = $(this).data("pay");
	});
    type = 'alipay';
	$("#submit").click(function(){
		var price = parseFloat($("#amount").val());
		console.log("Will use the "+type+" method to recharge the "+price+" CNY");
		if(isNaN(price)){
			$("#result").modal();
			$("#msg").html("Illegal amount!");
		}
		$.ajax({
			'url':"/user/payment/purchase",
			'data':{
				'price':price,
				'type':type,
			},
			'dataType':'json',
			'type':"POST",
			success:function(data){
				console.log(data);
				if(data.errcode==-1){
					$("#result").modal();
					$("#msg").html(data.errmsg);
				}
				if(data.errcode==0){
					pid = data.pid;
					if(type=="wepay"){
						$("#result").modal();
						$("#msg").html('<div class="text-center">Use WeChat to scan QR code to pay.<div id="dmy" style="padding-top:  10px;"></div></div>');
                        qrcode.clear();
                        qrcode.makeCode(data.code);
                        setTimeout(f, 2000);
					}else if(type=="alipay"){
						$("#result").modal();
						$("#msg").html("Jumping to Alipay..."+data.code);
					}else if(type=="qqpay"){
						$("#result").modal();
						$("#msg").html('<div class="text-center">Use QQ to scan the QR code to pay.<div id="dmy"></div></div>');
                        qrcode.clear();
                        qrcode.makeCode(data.code);
                        setTimeout(f, 2000);
					}
				}
			}
		});
	});
}
</script>
