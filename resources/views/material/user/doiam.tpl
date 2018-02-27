<div class="card-inner">
	<p class="card-heading">充值</p>
	您的余额:{$user->money}
	<h5>支付方式:</h5>
	<nav class="tab-nav margin-top-no">
		<ul class="nav nav-list">
			{if $enabled['wepay']}
				<li class="active">
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="wepay">微信支付</a>
				</li>
			{/if}
			{if $enabled['alipay']}
				<li>
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="alipay">支付宝</a>
				</li>
			{/if}
			{if $enabled['qqpay']}
				<li>
					<a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="qqpay">QQ支付</a>
				</li>
			{/if}
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
		<button class="btn btn-flat waves-attach" id="submit" ><span class="icon">check</span>&nbsp;充值</button>
	</div>
</div>
<script>
	var type = "wepay";
	var pid = 0;
window.onload = function(){
	$('body').append("<script src=\" \/assets\/public\/js\/jquery.qrcode.min.js \"><\/script>");
	$(".type").click(function(){
		type = $(this).data("pay");
	});
	$("#submit").click(function(){
		var price = parseFloat($("#amount").val());
		console.log("将要使用"+type+"方法充值"+price+"元")
		if(isNaN(price)){
			$("#result").modal();
			$("#msg").html("非法的金额!");
		}
		$.ajax({
			'url':"/user/doiam",
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
						$("#msg").html('<div class="text-center">使用微信扫描二维码支付.<div id="dmy" style="padding-top:  10px;"></div></div>');
						$("#dmy").qrcode({
							"text": data.code
						});
					}else if(type=="alipay"){
						$("#result").modal();
						$("#msg").html("正在跳转到支付宝..."+data.code);
					}else if(type=="qqpay"){
						$("#result").modal();
						$("#msg").html('<div class="text-center">使用QQ扫描二维码支付.<div id="dmy"></div></div>');
						$("#dmy").qrcode({
							"text": data.code
						});
					}
				}
			}
		});
		function f(){
			$.ajax({
				type: "POST",
				url: "/doiam/status",
				dataType: "json",
				data: {
					pid:pid
				},
				success: function (data) {
					if (data.status) {
						clearTimeout(tid);
						$("#result").modal();
						$("#msg").html("充值成功！");
						window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
					}
				}
			});
			tid = setTimeout(f, 1000);
		}
		setTimeout(f, 2000);
	});
}
</script>
