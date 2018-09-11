{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<center><h2 class="content-heading">用户查询</h2></center>
			</div>
		</div>
		<div class="container">
				<section class="content-inner margin-top-no">



					<div class="card">
						<div class="card-main">
							<div class="card-inner">


								<p>快速查看用户状态</p>

			

							</div>
						</div>
					</div>



					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="username">用户名</label>
									<input class="form-control" id="username" type="text">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="email">邮箱</label>
									<input class="form-control" id="email" type="text">
								</div>

								<div class="form-group form-group-label">
									<label class="floating-label" for="port">端口</label>
									<input class="form-control" id="port" type="number">
								</div>

								

			


								<div class="form-group">
									<div class="row">
										<div class="col-md-10 col-md-push-1">
											<button id="find" type="submit" class="btn btn-block btn-brand waves-attach waves-light">查找</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
{if $view==1}
	                                        <div class="card">
                                                <div class="card-main">
                                                        <div class="card-inner">
														<p>用户名：{$userf->user_name}</p>
                                                        <p>邮箱：{$userf->email}</p>
                                                        <p>端口：{$userf->port}</p>
<hr>
                                                        <p>等级：{$userf->class}</p>
                                                        <p>账户过期时间：{$userf->expire_in}</p>   
														<p>等级过期时间：{$userf->class_expire}</p>
<hr>
                                                        <p>总流量：{$userf->enableTraffic()}</p>
                                                        <p>已用流量：{$userf->LastusedTraffic()}</p>
                                                        <p>金钱：{$userf->money}</p>
<hr>
														<a class="btn btn-brand btn-flat waves-attach" href="/admin/user/{$userf->id}/edit" target="_blank" ><font style="color:green"><span class="icon">check</span>&nbsp;编辑</font></a>
                                                        &nbsp;&nbsp;<a class="btn btn-brand btn-flat waves-attach" id="delete"  ><font style="color:red"><span class="icon">close</span>&nbsp;删除</font></a>

                                                       
                                               
	
							</div>
						</div>
						</div>

				<div aria-hidden="true" class="modal modal-va-middle fade" id="delete_modal" role="dialog" tabindex="-1">
					<div class="modal-dialog modal-xs">
						<div class="modal-content">
							<div class="modal-heading">
								<a class="modal-close" data-dismiss="modal">×</a>
								<h2 class="modal-title">确认要删除？</h2>
							</div>
							<div class="modal-inner">
								<p>请您确认。</p>
							</div>
							<div class="modal-footer">
								<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button">取消</button><button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" id="delete_input" type="button">确定</button></p>
							</div>
						</div>
					</div>
				</div>
{/if}
					{include file='dialog.tpl'}
	

			</div>



	</main>












{include file='admin/footer.tpl'}





<script>



$(document).ready(function () {
		

		$("#find").click(function () {

				

	      $.ajax({
		          type: "POST",
		          url: "/admin/finduser",
		          dataType: "json",
		          data: {
		          username: $("#username").val(),
		          email: $("#email").val(),
			  port: $("#port").val(),

		          },
		          success: function (data) {
		              if (data.ret) {
		                  $("#result").modal();
		                  $("#msg").html(data.msg);
		                  window.setTimeout(location.href='/admin/find?id='+data.id,1000);
		              }else {
                        	$("#result").modal();
				$("#msg").html(data.msg);
				}
		              // window.location.reload();
		          },
		          error: function (jqXHR) {
                            $("#result").modal();
		            $("#msg").html("发生错误：" + jqXHR.status + data.msg);
		          }
	      })
		})

})


</script>
{if $view==1}
<script>



$(document).ready(function () {


$("#delete").click(function () {

 $("#delete_modal").modal();

})

	function delete_id(){
		$.ajax({
			type:"DELETE",
			url:"/admin/user",
			dataType:"json",
			data:{ 
				id:{$userf->id}
			},
			success:function(data){
				if(data.ret){
					$("#result").modal();
					$("#msg").html(data.msg);
					window.setTimeout(location.href='/admin/find',3000);
				}else{
					$("#result").modal();
					$("#msg").html(data.msg);
				}
			},
			error:function(jqXHR){
				$("#result").modal();
				$("#msg").html(data.msg+"  发生错误了。");
			}
		});
	}

	$("#delete_input").click(function(){
		delete_id();
	});



})
</script>

{/if}



