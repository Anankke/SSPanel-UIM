{include file='header.tpl'}


			<!-- 新的 -->
			<div class="authpage">
				<div class="container">
					
						<div class="auth-main auth-row auth-col-one">
							<div class="auth-top auth-row">
								<a class="boardtop-left" href="/">
									<div>Home</div>
								</a>
								<div class="auth-logo">
									<img src="/images/authlogo.jpg" alt="">
								</div>
								<a href="/auth/login" class="boardtop-right">
									<div>Login</div>
								</a>
							</div>
							<div class="auth-row">
								<div class="form-group-label auth-row row-login">
									<label class="floating-label" for="email">E-mail</label>
									<input class="form-control maxwidth-auth" id="email" type="text">
								</div>
							</div>
							
							<div class="btn-auth auth-row">
								<button id="reset" type="submit" class="btn btn-block btn-brand waves-attach waves-light">Reset password</button>
							</div>
							<div class="auth-help auth-row">
								<div class="auth-help-table auth-row auth-reset">
									<a href="" onclick="return false;" data-toggle='modal' data-target='#email_nrcy_modal'>Can't receive the verification code? Click here</a>
								</div>
							</div>
							<div class="auth-bottom auth-row auth-reset">
								<div class="tgauth">
								<p>Please take good care of your login password.</p>	
								</div>
							</div>
						</div>
				
					<div class="card auth-tg">
						<div class="card-main">
							
						</div>
					</div>
				</div>
			</div>
										
								
						{include file='dialog.tpl'}
						
						<div aria-hidden="true" class="modal modal-va-middle fade" id="email_nrcy_modal" role="dialog" tabindex="-1">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-heading">
										<h2 class="modal-title">Can't receive the verification code?</h2>
									</div>
									<div class="modal-inner">
										{include file='email_nrcy.tpl'}
									</div>
									<div class="modal-footer">
										<p class="text-right">
										<button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button">Thanks</button>
                                      </p>
									</div>
								</div>
							</div>
						</div>
						
					
	
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
                       window.setTimeout("location.href='/auth/login'", 2000);
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
                reset();
            }
        });
        $("#reset").click(function(){
            reset();
        });
    })
</script>