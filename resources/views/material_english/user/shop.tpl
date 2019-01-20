





{include file='user/main.tpl'}





	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Product list</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>Items cannot be stacked, and new items will cover the effects of old items.</p>
								<p>List of all items in the system. The period of validity is calculated from the current date.</p>
								<p>Current account balance: <code>{$user->money}</code> CNY</p>
							</div>
						</div>
					</div>

					<div class="ui-switch">
                         <div class="card">
							 <div class="card-main">
								 <div class="card-inner ui-switch">
										<div class="switch-btn" id="switch-cards"><a href="#" onclick="return false"><i class="mdui-icon material-icons">apps</i></a></div>
										<div class="switch-btn" id="switch-table"><a href="#" onclick="return false"><i class="mdui-icon material-icons">dehaze</i></a></div>
								 </div>
							 </div>
						 </div>
					</div>
						
            <div class="shop-flex">
				
				{foreach $shops as $shop}
                  <div class="card">
					  <div class="card-main">
								<div class="shop-name">{$shop->name}</div>
								<div class="shop-price">{$shop->price}</div>
								<div class="shop-tat">
									<span>{$shop->bandwidth()}</span> / <span>{$shop->class_expire()}</span>
								</div>
								<div class="shop-cube">
									<div>
										<div class="cube-detail">
											<span>Lv.</span>{$shop->user_class()}
										</div>
										<div class="cube-title">
											VIP
										</div>
									</div>
									<div>
										<div class="cube-detail">
											{if {$shop->connector()} == '0' }Unlimited{else}{$shop->connector()}<span> clients</span>{/if}
										</div>
										<div class="cube-title">
											Number of clients
										</div>
									</div>
									<div>
										<div class="cube-detail">
											{if {$shop->speedlimit()} == '0' }Unlimited{else}{$shop->speedlimit()}<span> Mbps</span>{/if}
										</div>
										<div class="cube-title">
											Speed
										</div>
									</div>

								</div>
								<div class="shop-content">
									<div class="shop-content-left">Account validity period:</div><div class="shop-content-right">{$shop->expire()}<span>days</span></div>
									<div class="shop-content-left">Reset cycle:</div><div class="shop-content-right">{if {$shop->reset()} == '0' }N / A{else}{$shop->reset_exp()}<span>days</span>{/if}</div>
									<div class="shop-content-left">Reset frequency:</div><div class="shop-content-right">{if {$shop->reset()} == '0' }N / A{else}{$shop->reset_value()}<span>G</span> / {$shop->reset()}<span>days</span>{/if}</div>
								</div>
								<div class="shop-content-extra">
									{foreach $shop->content_extra() as $service}
									<div><span class="icon">{$service[0]}</span> {$service[1]}</div>
									{/foreach}
								</div>
								<a class="btn btn-brand-accent shop-btn" href="javascript:void(0);" onClick="buy('{$shop->id}',{$shop->auto_renew})">Buy</a>
					  </div>
				  </div>
				{/foreach}
				
				<div class="flex-fix3"></div>
				<div class="flex-fix4"></div>
			</div>

            <div class="shop-table">
				
					{foreach $shops as $shop}
					<div class="shop-gridarea">
                        <div class="card">
								<div>
									<div class="shop-name"> <span>{$shop->name}</span></div>
									<div class="card-tag tag-gold">VIP {$shop->user_class()}</div>
									<div class="card-tag tag-orange">¥ {$shop->price}</div>
									<div class="card-tag tag-cyan">{$shop->bandwidth()} G</div>
									<div class="card-tag tag-blue">{$shop->class_expire()} days</div>
								</div>
								<div>
								<i class="material-icons">expand_more</i>
								</div>	
						</div>
						<a class="btn btn-brand-accent shop-btn" href="javascript:void(0);" onClick="buy('{$shop->id}',{$shop->auto_renew})">Buy</a>
						
						<div class="shop-drop dropdown-area">
							<div class="card-tag tag-black">Account validity period</div> <div class="card-tag tag-blue">{$shop->expire()} days</div>
							{if {$shop->reset()} == '0' }
							<div class="card-tag tag-black">Reset cycle</div> <div class="card-tag tag-blue">N/A</div>
							{else}
							<div class="card-tag tag-black">Reset cycle</div> <div class="card-tag tag-blue">{$shop->reset_exp()} days</div>
							<div class="card-tag tag-black">Reset frequency</div><div class="card-tag tag-blue">{$shop->reset_value()}G/{$shop->reset()}days</div>
							{/if}
								{if {$shop->speedlimit()} == '0' }
								<div class="card-tag tag-black">Port speed</div> <div class="card-tag tag-blue">Unlimited</div>
								{else}
								<div class="card-tag tag-black">Port speed limited</div> <div class="card-tag tag-blue">{$shop->speedlimit()} Mbps</div>
								{/if}
								{if {$shop->connector()} == '0' }
								<div class="card-tag tag-black">Number of clients</div> <div class="card-tag tag-blue">Unlimited</div>
								{else}
								<div class="card-tag tag-black">Clients limited</div> <div class="card-tag tag-blue">{$shop->connector()} clients</div>
								{/if}
						</div>
					</div>
					{/foreach}
				
            </div>
					
					
					
					<div aria-hidden="true" class="modal modal-va-middle fade" id="coupon_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
								<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
									<h2 class="modal-title">Do you have a coupon code?</h2>
								</div>
								<div class="modal-inner">
									<div class="form-group form-group-label">
										<label class="floating-label" for="coupon">If yes, please enter here. If not, click OK.</label>
										<input class="form-control maxwidth-edit" id="coupon" type="text">
									</div>
								</div>
								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal" id="coupon_input" type="button">OK</button></p>
								</div>
							</div>
						</div>
					</div>
					
					
					<div aria-hidden="true" class="modal modal-va-middle fade" id="order_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
								<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
									<h2 class="modal-title">Order Confirmation</h2>
								</div>
								<div class="modal-inner">
									<p id="name">Product name:</p>
									<p id="credit">Discount amount:</p>
									<p id="total">Total amount:</p>

									<div class="checkbox switch">
										<label for="disableothers">
											<input checked class="access-hide" id="disableothers" type="checkbox">
											<span class="switch-toggle"></span>Close old package auto renewal
										</label>
									</div>
									<br/>
									<div class="checkbox switch" id="autor">
										<label for="autorenew">
											<input checked class="access-hide" id="autorenew" type="checkbox">
											<span class="switch-toggle"></span>Automatic renewal
										</label>
									</div>
									
								</div>
								
								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal" id="order_input" type="button">OK</button></p>
								</div>
							</div>
						</div>
					</div>
					
					{include file='dialog.tpl'}
	
			</div>
			
			
			
		</div>
	</main>









{include file='user/footer.tpl'}


<script>
function buy(id,auto) {
	if(auto==0)
	{
		document.getElementById('autor').style.display="none";
	}
	else
	{
		document.getElementById('autor').style.display="";
	}
	shop=id;
	$("#coupon_modal").modal();
}

;(function(){
	
	//UI切换
	let elShopCard = $$.querySelector(".shop-flex");
	let elShopTable = $$.querySelector(".shop-table");
	
	let switchToCard = new UIswitch('switch-cards',elShopTable,elShopCard,'flex','tempshop');
	switchToCard.listenSwitch();
    
	let switchToTable = new UIswitch('switch-table',elShopCard,elShopTable,'flex','tempshop');
	switchToTable.listenSwitch();

	switchToCard.setDefault();
	switchToTable.setDefault();
	
	//手风琴
	let dropDownButton = document.querySelectorAll('.shop-table .card');
	let dropDownArea = document.querySelectorAll('.dropdown-area');
	let arrows = document.querySelectorAll('.shop-table .card i');
	
	for (let i=0;i<dropDownButton.length;i++) {
		rotatrArrow(dropDownButton[i],arrows[i]);
		custDropdown(dropDownButton[i], dropDownArea[i]);
	}

})();
    

$("#coupon_input").click(function () {
		$.ajax({
			type: "POST",
			url: "coupon_check",
			dataType: "json",
			data: {
				coupon: $("#coupon").val(),
				shop: shop
			},
			success: function (data) {
				if (data.ret) {
					$("#name").html("Product name: "+data.name);
					$("#credit").html("Discount amount: "+data.credit);
					$("#total").html("Total amount: "+data.total);
					$("#order_modal").modal();
				} else {
					$("#result").modal();
					$("#msg").html(data.msg);
				}
			},
			error: function (jqXHR) {
				$("#result").modal();
                $("#msg").html(data.msg+"  An error occurred.");
			}
		})
	});
	
$("#order_input").click(function () {

		if(document.getElementById('autorenew').checked)
		{
			var autorenew=1;
		}
		else
		{
			var autorenew=0;
		}

		if(document.getElementById('disableothers').checked){
			var disableothers=1;
		}
		else{
			var disableothers=0;
		}
			
		$.ajax({
			type: "POST",
			url: "buy",
			dataType: "json",
			data: {
				coupon: $("#coupon").val(),
				shop: shop,
				autorenew: autorenew,
				disableothers:disableothers
			},
			success: function (data) {
				if (data.ret) {
					$("#result").modal();
					$("#msg").html(data.msg);
					window.setTimeout("location.href='/user/shop'", {$config['jump_delay']});
				} else {
					$("#result").modal();
					$("#msg").html(data.msg);
				}
			},
			error: function (jqXHR) {
				$("#result").modal();
                $("#msg").html(data.msg+"  An error occurred.");
			}
		})
	});

</script>