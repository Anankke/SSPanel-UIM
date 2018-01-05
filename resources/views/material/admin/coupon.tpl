





{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">优惠码</h1>
			</div>
		</div>
		<div class="container">
				<section class="content-inner margin-top-no">



					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="prefix">优惠码前缀</label>
									<input class="form-control" id="prefix" type="text">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="credit">优惠码额度(百分比，九折就填 10 )</label>
									<input class="form-control" id="credit" type="text">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="expire">优惠码有效期(h)</label>
									<input class="form-control" id="expire" type="number" value="1">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="shop">优惠码可用商品ID，不填即为所有商品可用，多个的话用英文半角逗号分割</label>
									<input class="form-control" id="shop" type="text">
								</div>

								<div class="form-group form-group-label">
									<div class="checkbox switch">
										<label for="onetime">
											<input class="access-hide" id="onetime" type="checkbox"><span class="switch-toggle"></span>一次性的,只在用户当次购买时有效
										</label>
									</div>
								</div>


								<div class="form-group">
									<div class="row">
										<div class="col-md-10 col-md-push-1">
											<button id="coupon" type="submit" class="btn btn-block btn-brand waves-attach waves-light">生成</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card margin-bottom-no">
						<div class="card-main">
							<div class="card-inner">
								<p class="card-heading">优惠码</p>
								<p>显示表项:
									{include file='table/checkbox.tpl'}
								</p>
								<div class="card-table">
									<div class="table-responsive">
										{include file='table/table.tpl'}
									</div>
								</div>
							</div>
						</div>
					</div>

					{include file='dialog.tpl'}


			</div>



	</main>












{include file='admin/footer.tpl'}





<script>

{include file='table/js_1.tpl'}

$(document).ready(function () {
		{include file='table/js_2.tpl'}

		$("#coupon").click(function () {

				if(document.getElementById('onetime').checked)
				{
						var onetime=1;
				}
				else
				{
						var onetime=0;
				}

	      $.ajax({
		          type: "POST",
		          url: "/admin/coupon",
		          dataType: "json",
		          data: {
		          prefix: $("#prefix").val(),
		          credit: $("#credit").val(),
							shop: $("#shop").val(),
							onetime: onetime,
		          expire: $("#expire").val()
		          },
		          success: function (data) {
		              if (data.ret) {
		                  $("#result").modal();
		                  $("#msg").html(data.msg);
		                  window.setTimeout("location.href='/admin/coupon'", {$config['jump_delay']});
		              }
		              // window.location.reload();
		          },
		          error: function (jqXHR) {
		              alert("发生错误：" + jqXHR.status);
		          }
	      })
		})
})
</script>
