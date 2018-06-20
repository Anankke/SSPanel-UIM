
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
									<!-- 这里可以取消掉注释换logo图。
									<h1 class="card-heading"><img src="/images/register.jpg" height=100% width=100% /></h1>
									-->
									<h1 class="card-heading">
										<div class="text" style=" text-align:center;">
											欢迎来到
										</div>
										<div class="text" style=" text-align:center;font-weight: bold;">
											{$config["appName"]}
										</div>
									</h1>
									</div>
								</div>
								<div class="card-inner">


										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="name">昵称</label>
													<input class="form-control" id="name" type="text">
												</div>
											</div>
										</div>

										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="email">邮箱(唯一凭证请认真对待)</label>
													<input class="form-control" id="email" type="text">
												</div>
											</div>
										</div>
										{*



                                  			<!--<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
											<label class="floating-label" for="theme">主题</label>
											<select id="theme" class="form-control">

													<option value="{$theme}">{$theme}</option>

													</select>
												</div>
											</div>
										</div>-->




                                  *}
										{if $enable_email_verify == 'true'}
										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="email_code">邮箱验证码</label>
													<input class="form-control" id="email_code" type="text" onKeypress="javascript:if(event.keyCode == 32)event.returnValue = false;">
													<button id="email_verify" class="btn btn-block btn-brand-accent waves-attach waves-light">点击获取验证码</button>
													<a href="" onclick="return false;" data-toggle='modal' data-target='#email_nrcy_modal'>收不到验证码？点击这里</a>
												</div>
											</div>
										</div>

                                  {/if}


										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="passwd">密码</label>
													<input class="form-control" id="passwd" type="password">
												</div>
											</div>
										</div>

										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="repasswd">重复密码</label>
													<input class="form-control" id="repasswd" type="password">
												</div>
											</div>
										</div>


										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="imtype">选择您的联络方式</label>
													<select class="form-control" id="imtype">
														<option></option>
														<option value="1">微信</option>
														<option value="2">QQ</option>
														<option value="3">Facebook</option>
														<option value="4">Telegram</option>
													</select>
												</div>
											</div>
										</div>


										<div class="form-group form-group-label">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<label class="floating-label" for="wechat">在这输入联络方式账号</label>
													<input class="form-control" id="wechat" type="text">
												</div>
											</div>
										</div>


											<div class="form-group form-group-label">
												<div class="row">
													<div class="col-md-10 col-md-push-1">
														<label class="floating-label" for="code">邀请码
														{if $enable_invite_code == 'false'}
														(可选)
														{else}
														(必填)
														{/if}</label>
														<input class="form-control" id="code" type="text">
													</div>
												</div>
											</div>

										{if $geetest_html != null}
											<div class="form-group form-group-label">
												<div class="row">
													<div class="col-md-10 col-md-push-1">
														<div id="embed-captcha"></div>
													</div>
												</div>
											</div>
										{/if}

										<div class="form-group">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<button id="tos" type="submit" class="btn btn-block btn-brand waves-attach waves-light">注册</button>
												</div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-md-10 col-md-push-1">
													<p>注册即代表同意<a href="/tos">服务条款</a>，以及保证所录入信息的真实性，如有不实信息会导致账号被删除。</p>
												</div>
											</div>
										</div>

								</div>
							</div>
						</div>
						<div class="clearfix">
							<p class="margin-no-top pull-left"><a class="btn btn-flat btn-brand waves-attach" href="/auth/login">已经注册？请登录</a></p>
						</div>




						{include file='dialog.tpl'}


						<div aria-hidden="true" class="modal modal-va-middle fade" id="tos_modal" role="dialog" tabindex="-1">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-heading">
										<h2 class="modal-title">注册 TOS</h2>
									</div>
									<div class="modal-inner">
										{include file='reg_tos.tpl'}
									</div>
									<div class="modal-footer">
										<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button" id="cancel">我不同意</button>
                                          <button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" id="reg" type="button">我同意</button>
                                      </p>
									</div>
								</div>
							</div>
						</div>

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
        function register(){

			document.getElementById("tos").disabled = true;

            $.ajax({
                type:"POST",
                url:"/auth/register",
                dataType:"json",
                data:{
                    email: $("#email").val(),
                    name: $("#name").val(),
                    passwd: $("#passwd").val(),
                    repasswd: $("#repasswd").val(),
					wechat: $("#wechat").val(),
					imtype: $("#imtype").val(),
					code: $("#code").val(){if $enable_email_verify == 'true'},
					emailcode: $("#email_code").val(){/if}{if $geetest_html != null},
					geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode
					{/if}
                },
                success:function(data){
                    if(data.ret == 1){
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        window.setTimeout("location.href='/auth/login'", {$config['jump_delay']});
                    }else{
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        setCookie('code','',0);
                        $("#code").val(getCookie('code'));
						document.getElementById("tos").disabled = false;
						{if $geetest_html != null}
						captcha.refresh();
						{/if}
                    }
                },
                error:function(jqXHR){
			$("#msg-error").hide(10);
			$("#msg-error").show(100);
			$("#msg-error-p").html("发生错误："+jqXHR.status);
			document.getElementById("tos").disabled = false;
			{if $geetest_html != null}
			captcha.refresh();
			{/if}
                }
            });
        }
        $("html").keydown(function(event){
            if(event.keyCode==13){
                $("#tos_modal").modal();
            }
        });

		{if $geetest_html != null}
		$('div.modal').on('shown.bs.modal', function() {
			$("div.gt_slider_knob").hide();
		});


		$('div.modal').on('hidden.bs.modal', function() {
			$("div.gt_slider_knob").show();
		});


		{/if}

		$("#reg").click(function(){
            register();
        });

		$("#tos").click(function(){
			{if $geetest_html != null}
			if(typeof validate == 'undefined')
			{
				$("#result").modal();
                $("#msg").html("请滑动验证码来完成验证。");
				return;
			}

			if (!validate) {
				$("#result").modal();
                $("#msg").html("请滑动验证码来完成验证。");
				return;
			}

			{/if}
            $("#tos_modal").modal();
        });
    })
</script>


{if $enable_email_verify == 'true'}
<script>
var wait=60;
function time(o) {
		if (wait == 0) {
			o.removeAttr("disabled");
			o.text("获取验证码");
			wait = 60;
		} else {
			o.attr("disabled","disabled");
			o.text("重新发送(" + wait + ")");
			wait--;
			setTimeout(function() {
				time(o)
			},
			1000)
		}
	}



    $(document).ready(function () {
        $("#email_verify").click(function () {
			time($("#email_verify"));

            $.ajax({
                type: "POST",
                url: "send",
                dataType: "json",
                data: {
                    email: $("#email").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
			$("#msg").html(data.msg);

                    } else {
                        $("#result").modal();
			$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
			$("#msg").html(data.msg+"     出现了一些错误。");
                }
            })
        })
    })
</script>
{/if}

{if $geetest_html != null}
<script>
	var handlerEmbed = function (captchaObj) {
        // 将验证码加到id为captcha的元素里

		captchaObj.onSuccess(function () {
		    validate = captchaObj.getValidate();
		});

		captchaObj.appendTo("#embed-captcha");

		captcha = captchaObj;
		// 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
    };

	initGeetest({
		gt: "{$geetest_html->gt}",
		challenge: "{$geetest_html->challenge}",
		product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
		offline: {if $geetest_html->success}0{else}1{/if} // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
	}, handlerEmbed);
</script>

{/if}

{*dumplin:aff链*}
<script>
	{*dumplin：轮子1.js读取url参数*}
	function getQueryVariable(variable)
	{
	       var query = window.location.search.substring(1);
	       var vars = query.split("&");
	       for (var i=0;i<vars.length;i++) {
	            	var pair = vars[i].split("=");
	            	if(pair[0] == variable){
	            		return pair[1];
	            	}
	       }
	       return "";
	}

	{*dumplin:轮子2.js写入cookie*}
	function setCookie(cname,cvalue,exdays)
	{
	  var d = new Date();
	  d.setTime(d.getTime()+(exdays*24*60*60*1000));
	  var expires = "expires="+d.toGMTString();
	  document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	{*dumplin:轮子3.js读取cookie*}
	function getCookie(cname)
	{
	  var name = cname + "=";
	  var ca = document.cookie.split(';');
	  for(var i=0; i<ca.length; i++) 
	  {
	    var c = ca[i].trim();
	    if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	  }
	  return "";
	}

	{*dumplin:读取url参数写入cookie，自动跳转隐藏url邀请码*}
	if (getQueryVariable('code')!=''){
		setCookie('code',getQueryVariable('code'),30);
		window.location.href='/auth/register'; 
	}

	{*dumplin:读取cookie，自动填入邀请码框*}
	if ((getCookie('code'))!=''){
		$("#code").val(getCookie('code'));
	}

</script>