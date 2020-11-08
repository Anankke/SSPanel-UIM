{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">邀请</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">

            <div class="card">
                <div class="card-main">
                    <div class="card-inner">
                        <p>公共邀请码功能已废弃，如需开放注册请在 .config.php 中将 register_mode 项目设置为 open </p>
                    </div>
                </div>
            </div>

            <div class="card">
				<div class="card-main">
					<div class="card-inner">
						<div class="form-group form-group-label">
							<label class="floating-label" for="userid">需要修改邀请者的用户</label>
							<input class="form-control maxwidth-edit" id="userid" type="text">
							<p class="form-control-guide"><i class="material-icons">info</i>填写用户的ID</p>
						</div>
						<div class="form-group form-group-label">
							<label class="floating-label" for="refid">邀请者的ID</label>
							<input class="form-control maxwidth-edit" id="refid" type="number">
						</div>
					</div>
					<div class="card-action">
						<div class="card-action-btn pull-left">
							<a class="btn btn-flat waves-attach" id="confirm"><span class="icon">check</span>&nbsp;更改</a>
						</div>
					</div>
				</div>
			</div>
            <div class="card">
                <div class="card-main">
                    <div class="card-inner">

                        <div class="form-group form-group-label">
                            <label class="floating-label" for="uid">需要增加邀请链接数量的用户</label>
                            <input class="form-control maxwidth-edit" id="uid" type="text">
                            <p class="form-control-guide"><i class="material-icons">info</i>填写用户的ID，或者用户的完整邮箱</p>
                        </div>
                        <div class="form-group form-group-label">
                            <label class="floating-label" for="prefix">邀请链接数量</label>
                            <input class="form-control maxwidth-edit" id="num" type="number">
                        </div>
                    </div>
                    <div class="card-action">
                        <div class="card-action-btn pull-left">
                            <a class="btn btn-flat waves-attach" id="invite"><span class="icon">check</span>&nbsp;增加</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card margin-bottom-no">
                <div class="card-main">
                    <div class="card-inner">
                        <p class="card-heading">返利记录</p>
                        <p>显示表项: {include file='table/checkbox.tpl'}
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
    $$.getElementById('invite').addEventListener('click', () => {
        $.ajax({
            type: "POST",
            url: "/admin/invite",
            dataType: "json",
            data: {
                prefix: $$getValue('invite'),
                uid: $$getValue('uid'),
                num: $$getValue('num'),
            },
            success: data => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/admin/invite'", {$config['jump_delay']} );
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
                // window.location.reload();
            },
            error: jqXHR => {
                alert(`发生错误：${
                        jqXHR.status
                        }`);
            }
        })
    })

    $$.getElementById('confirm').addEventListener('click', () => {
        $.ajax({
            type: "POST",
            url: "/admin/chginvite",
            dataType: "json",
            data: {
                prefix: $$.getElementById('confirm').value,
                userid: $$.getElementById('userid').value,
                refid: $$.getElementById('refid').value,
            },
            success: data => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/admin/invite'", {$config['jump_delay']} );
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
                // window.location.reload();
            },
            error: jqXHR => {
                alert(`发生错误：${ldelim}jqXHR.status{rdelim}`);
            }
        })
    })

    window.addEventListener('load', () => {
        {include file='table/js_2.tpl'}
    });
</script>