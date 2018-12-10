


{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">添加公告</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
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
					
					
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								
								<div class="form-group">
									<div class="row">
										<div class="col-md-10 col-md-push-1">
                                         <div class="form-group form-group-label">
                                          	<label class="floating-label" for="vip">VIP等级（发送给高于这个等级的用户 0为不分级）</label>
											<input class="form-control" id="vip" type="text" name="vip">
                                           <div class="checkbox switch">
											<label for="issend">
												<input class="access-hide" id="issend" type="checkbox" name="issend"><span class="switch-toggle"></span>是否发送邮件
											</label>
											</div>
										</div>
                                         
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

<script src="https://cdn.jsdelivr.net/npm/editor.md@1.5.0/editormd.min.js"></script>
<script>
    $(document).ready(function () {
        function submit() {
          
          	if(document.getElementById('issend').checked)
			{
				var issend=1;
			}
			else
			{
				var issend=0;
			}
          
            $.ajax({
                type: "POST",
                url: "/admin/announcement",
                dataType: "json",
                data: {
                    content: editor.getHTML(),
					markdown: editor.getMarkdown(),
                  	vip: $("#vip").val(),
                  	issend: issend
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
            submit();
        });
    });
	
    $(function() {
        editor = editormd("editormd", {
             path : "https://cdn.jsdelivr.net/npm/editor.md@1.5.0/lib/", // Autoload modules mode, codemirror, marked... dependents libs path
			height: 720,
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