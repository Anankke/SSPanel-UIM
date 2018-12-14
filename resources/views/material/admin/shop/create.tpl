
{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">添加商品</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="name">名称</label>
									<input class="form-control" id="name" type="text" >
								</div>


								<div class="form-group form-group-label">
									<label class="floating-label" for="price">价格</label>
									<input class="form-control" id="price" type="text" >
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="auto_renew">自动续订天数（0为不允许自动续订，其他为到了那么多天之后就会自动从用户的账户上划钱抵扣）</label>
									<input class="form-control" id="auto_renew" type="text" value="0">
								</div>


							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-main">
							<div class="card-inner">

								<div class="form-group form-group-label">
									<label class="floating-label" for="bandwidth">流量（GB）</label>
									<input class="form-control" id="bandwidth" type="text">
								</div>

								<div class="form-group form-group-label">
									<div class="checkbox switch">
										<label for="auto_reset_bandwidth">
											<input class="access-hide" id="auto_reset_bandwidth" type="checkbox"><span class="switch-toggle"></span>续费时自动重置用户流量为上面这个流量值
										</label>
									</div>
								</div>

							</div>
						</div>
					</div>


					<div class="card">
						<div class="card-main">
							<div class="card-inner">

								<div class="form-group form-group-label">
									<label class="floating-label" for="expire">账户有效期天数</label>
									<input class="form-control" id="expire" type="text" value="0">
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-main">
							<div class="card-inner">

								<div class="form-group form-group-label">
									<label class="floating-label" for="class">等级</label>
									<input class="form-control" id="class" type="text" value="0">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="class_expire">等级有效期天数</label>
									<input class="form-control" id="class_expire" type="text" value="0">
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="reset_exp">多少天内</label>
									<input class="form-control" id="reset_exp" type="number" value="0">
								</div>


								<div class="form-group form-group-label">
									<label class="floating-label" for="reset">每多少天</label>
									<input class="form-control" id="reset" type="number" value="0">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="reset_value">重置流量为多少G</label>
									<input class="form-control" id="reset_value" type="number" value="0">
								</div>
							</div>
						</div>
					</div>


					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="speedlimit">端口限速</label>
									<input class="form-control" id="speedlimit" type="number" value="0">
								</div>


								<div class="form-group form-group-label">
									<label class="floating-label" for="connector">IP限制</label>
									<input class="form-control" id="connector" type="number" value="0">
								</div>
							</div>
						</div>
					</div>


					<div class="card">
						<div class="card-main">
							<div class="card-inner">

								<div class="form-group">
									<div class="row">
										<div class="col-md-10 col-md-push-1">
											<button id="submit" type="submit" class="btn btn-block btn-brand waves-attach waves-light">添加</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					{include file='dialog.tpl'}




			</div>



		</div>
	</main>











{include file='admin/footer.tpl'}



<script>
    $(document).ready(function () {
        function submit() {
			if(document.getElementById('auto_reset_bandwidth').checked)
			{
				var auto_reset_bandwidth=1;
			}
			else
			{
				var auto_reset_bandwidth=0;
			}

            $.ajax({
                type: "POST",
                url: "/admin/shop",
                dataType: "json",
                data: {
                    name: $("#name").val(),
										auto_reset_bandwidth: auto_reset_bandwidth,
                    price: $("#price").val(),
                    auto_renew: $("#auto_renew").val(),
                    bandwidth: $("#bandwidth").val(),
                    speedlimit: $("#speedlimit").val(),
                    connector: $("#connector").val(),
                    expire: $("#expire").val(),
                    class: $("#class").val(),
										class_expire: $("#class_expire").val(),
										reset: $("#reset").val(),
										reset_value: $("#reset_value").val(),
										reset_exp: $("#reset_exp").val(),
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
                        $("#msg").html(data.msg+"  发生错误了。");
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                login();
            }
        });
        $("#submit").click(function () {
            submit();
        });
    })
</script>
