{include file='admin/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">查看工单</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">
				
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="content">内容</label>
									<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/editor.md@1.5.0/css/editormd.min.css" />
									<div id="editormd">
										<textarea style="display:none;" id="content"></textarea>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
				<div aria-hidden="true" class="modal modal-va-middle fade" id="changetouser_modal" role="dialog" tabindex="-1">
					<div class="modal-dialog modal-xs">
						<div class="modal-content">
							<div class="modal-heading">
								<a class="modal-close" data-dismiss="modal">×</a>
								<h2 class="modal-title">确认要切换为该用户？</h2>
							</div>
							<div class="modal-inner">
								<p>请您确认。</p>
							</div>
							<div class="modal-footer">
								<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button">取消</button><button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" id="changetouser_input" type="button">确定</button></p>
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
                                            <button id="close" type="submit" class="btn btn-block btn-brand-accent waves-attach waves-light">添加并关闭</button>
                                            <button id="close_directly" type="submit" class="btn btn-block btn-brand-accent waves-attach waves-light">直接关闭</button>
											<a class="btn btn-block btn-brand waves-attach waves-light" id="changetouser" href="javascript:void(0);" onClick="changetouser_modal_show()">切换为该用户</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					{$ticketset->render()}
					{foreach $ticketset as $ticket}
					<div class="card">
						<aside class="card-side pull-left"><img alt="alt text for John Smith avatar" src="{$ticket->User()->gravatar}"></span></br>{$ticket->User()->user_name}</aside>
						<div class="card-main">
							<div class="card-inner">
								{$ticket->content}
							</div>
							<div class="card-action"> {$ticket->datetime()}</div>
						</div>
					</div>
					{/foreach}
					{$ticketset->render()}
					
					
					{include file='dialog.tpl'}

			</div>

		</div>
	</main>

{include file='admin/footer.tpl'}

<script src="https://cdn.jsdelivr.net/npm/editor.md@1.5.0/editormd.min.js"></script>
<script>
    function changetouser_modal_show() {
        $("#changetouser_modal").modal();
    }
    window.addEventListener('load', () => {
        function submit() {
			$("#result").modal();
            $$.getElementById('msg').innerHTML = `正在提交。`;
            $.ajax({
                type: "PUT",
                url: "/admin/ticket/{$id}",
                dataType: "json",
                data: {
                    content: editor.getHTML(),
                    status
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
                    $("#result").modal();
					$$.getElementById('msg').innerHTML = `发生错误：${ldelim}jqXHR.status{rdelim}`;
                }
            });
        }

        $$.getElementById('submit').addEventListener('click', () => {
            status = 1;
            submit();
        });

        $$.getElementById('close').addEventListener('click', () => {
            status = 0;
            submit();
        });

        $$.getElementById('close_directly').addEventListener('click', () => {
            status = 0;
            $("#result").modal();
            $$.getElementById('msg').innerHTML = `正在提交。`;
            $.ajax({
                type: "PUT",
                url: "/admin/ticket/{$id}",
                dataType: "json",
                data: {
                    content: '这条工单已被关闭',
                    status
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
                    $("#result").modal();
					$$.getElementById('msg').innerHTML = `发生错误：${ldelim}jqXHR.status{rdelim}`;
                }
            });
        });

	function changetouser_id(){
		$.ajax({
			type:"POST",
			url:"/admin/user/changetouser",
			dataType:"json",
			data:{
                userid: {$ticket->User()->id},
                adminid: {$user->id},
                local: '/admin/ticket/' + {$ticket->id} + '/view'
			},
            success: data =>{
                if (data.ret) {
					$("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user'", {$config['jump_delay']});
                } else {
					$("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
				}
			},
            error: jqXHR => {
				$("#result").modal();
                $$.getElementById('msg').innerHTML = `发生错误：${ldelim}jqXHR.status{rdelim}`;
			}
		});
	}

    $$.getElementById('changetouser_input').addEventListener('click', ()=>{
		changetouser_id();
	});

	});

    (() => {
        editor = editormd("editormd", {
            path : "https://cdn.jsdelivr.net/npm/editor.md@1.5.0/lib/", // Autoload modules mode, codemirror, marked... dependents libs path
            height: 450,
            saveHTMLToTextarea : true,
            emoji : true
        });

        /*
        // or
        var editor = editormd({
            id   : "editormd",
            path : "../lib/"
        });
        */
    })();

</script>
