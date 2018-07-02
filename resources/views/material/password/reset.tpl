{include file='header.tpl'}



<main class="content">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-lg-push-4 col-sm-6 col-sm-push-3">
					<section class="content-inner">
						<div class="card">
							<div class="card-main">
								<div class="card-header">
									<div class="card-inner">
										<h1 class="card-heading">重置密码</h1>
									</div>
								</div>
								<div class="card-inner">
									<p class="text-center">

									</p>
									
										
										
										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="email">邮箱</label>
													<input class="form-control" id="email" type="text">
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<button id="reset" type="submit" class="btn btn-block btn-brand waves-attach waves-light">重置密码</button>
													<a href="" onclick="return false;" data-toggle='modal' data-target='#email_nrcy_modal'>收不到验证码？点击这里</a>
												</div>
											</div>
										</div>
										
										
									
								</div>
							</div>
						</div>				
										
								
						{include file='dialog.tpl'}
						
						<div aria-hidden="true" class="modal modal-va-middle fade" id="email_nrcy_modal" role="dialog" tabindex="-1">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-heading">
										<h2 class="modal-title">收不到验证码？</h2>
									</div>
									<div class="modal-inner">
										{include file='email_nrcy.tpl'}
									</div>
									<div class="modal-footer">
										<p class="text-right">
										<button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button">我知道了</button>
                                      </p>
									</div>
								</div>
							</div>
						</div>
						
					</section>
				</div>
			</div>
		</div>
	</main>
	
{include file='footer.tpl'}


<script>
    $(document).ready(function(){
        function reset(){
			$("#result").modal();
            $("#msg").html("sending, please wait....");
            $.ajax({
                type:"POST",
                url:"/password/reset",
                dataType:"json",
                data:{
                    email: $("#email").val(),
                },
                success:function(data){
                    if(data.ret == 1){
                        $("#result").modal();
                        $("#msg").html(data.msg);
                       // window.setTimeout("location.href='/auth/login'", 2000);
                    }else{
                        $("#result").modal();
                        $("#msg").html(data.msg);
                    }
                },
                error:function(jqXHR){
                    $("#result").modal();
                    $("#msg").html(data.msg);
                }
            });
        }
        $("html").keydown(function(event){
            if(event.keyCode==13){
                login();
            }
        });
        $("#reset").click(function(){
            reset();
        });
    })
</script>