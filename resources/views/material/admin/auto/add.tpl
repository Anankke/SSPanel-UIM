
{include file='admin/main.tpl'}


	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">添加下发命令</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
				<section class="content-inner margin-top-no">
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="content">命令</label>
									<textarea class="form-control maxwidth-edit" id="content" rows="15"></textarea>
								</div>
								
								
								
								
							</div>
						</div>
					</div>
					
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="sign">GPG签名</label>
									<textarea class="form-control maxwidth-edit" id="sign" rows="15"></textarea>
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
    window.addEventListener('load', () => {
        function submit() {
            $.ajax({
                type: "POST",
                url: "/admin/auto",
                dataType: "json",
                data: {
                    content: $$getValue('content'),
					sign: $$getValue('sign')
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $$.getElementById('msg-error-p').innerHTML = `发生错误：${ldelim}jqXHR.status{rdelim}`;
                }
            });
        }

        $$.getElementById('submit').addEventListener('click', submit);
    });
</script>