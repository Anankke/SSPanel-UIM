{include file='admin/main.tpl'}
	<!-- https://sspanel3.org/optimization-work-order-system/ -->
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
									<link rel="stylesheet" href="/theme/material/editor/css/editormd.min.css" />
									<div id="editormd">
										<textarea style="display:none;" id="content"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="mdui-typo">
						<h3 style="color: #3f51b5">操作</h3>
					</div>
					
					<div class="mdui-card">
						<div class="mdui-card-actions">
							<div class="mdui-row-xs-2">
								<div class="mdui-col">
									<button id="submit" type="submit" class="mdui-btn mdui-btn-block mdui-color-pink-a200 mdui-ripple mdui-btn-raised">提交</button>
								</div>
								<div class="mdui-col">
									<button id="close" type="submit" class="mdui-btn mdui-btn-block mdui-color-indigo mdui-ripple mdui-btn-raised">提交并关闭</button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="mdui-typo">
						<h3 style="color: #3f51b5">内容</h3>
					</div>
					
					{$ticketset->render()}
					{foreach $ticketset as $ticket}
					<div class="mdui-chip">
					{if $ticket->User()->isAdmin()}
						<!-- <img class="mdui-chip-icon" src="https://i.loli.net/2018/06/17/5b2633c2d45da.png"/> -->
						<span class="mdui-chip-icon mdui-color-red"><i class="mdui-icon material-icons">person</i></span>
						<span class="mdui-chip-title">管理员</span>
					{else}
						<!-- <img class="mdui-chip-icon" src="https://i.loli.net/2018/06/04/5b150945bc54d.png"/> -->
						<span class="mdui-chip-icon mdui-color-green"><i class="mdui-icon material-icons">person</i></span>
						<span class="mdui-chip-title"><a href="/admin/user/{$ticket->userid}/edit">{$ticket->User()->user_name}</a></span>
					{/if}
					</div>
					<div class="mdui-chip">
						<span class="mdui-chip-icon mdui-color-blue"><i class="mdui-icon material-icons">access_time</i></span>
						<span class="mdui-chip-title">{$ticket->datetime()}</span>
					</div>
					</br></br>
					<div class="mdui-card">
						<div class="mdui-card-content">
							{$ticket->content}
						</div>
					</div></br>
					{/foreach}
					{$ticketset->render()}
					{include file='dialog.tpl'}
			</div>
		</div>
	</main>

{include file='admin/footer.tpl'}

<script src="/theme/material/editor/editormd.min.js"></script>
<script>
    $(document).ready(function () {
        function submit() {
			$("#result").modal();
            $("#msg").html("正在提交。");
            $.ajax({
                type: "PUT",
                url: "/admin/ticket/{$id}",
                dataType: "json",
                data: {
                    content: editor.getHTML(),
					title: $("#title").val(),
					status:status
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
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html("发生错误：" + jqXHR.status);
                }
            });
        }
		
        $("#submit").click(function () {
			status=1;
            submit();
        });
		
		$("#close").click(function () {
			status=0;
            submit();
        });
    });
	
    $(function() {
        editor = editormd("editormd", {
            path : "/theme/material/editor/lib/", // Autoload modules mode, codemirror, marked... dependents libs path
			height: 450,
			saveHTMLToTextarea : true
        });

        /*
        // or
        var editor = editormd({
            id   : "editormd",
            path : "../lib/"
        });
        */
    });
</script>