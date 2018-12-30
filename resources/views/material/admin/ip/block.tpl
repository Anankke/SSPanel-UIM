
{include file='admin/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">节点被封IP</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>这里是最近的节点上捕捉到的进行非法行为的IP。</p>
								<p>显示表项:
	                {include file='table/checkbox.tpl'}
	              </p>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="ip">要解封的IP</label>
									<input class="form-control maxwidth-edit" id="ip" type="text">
								</div>


							</div>

							<div class="card-action">
								<div class="card-action-btn pull-left">
									<a class="btn btn-flat waves-attach" id="unblock" ><span class="icon">check</span>&nbsp;解封</a>
								</div>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						{include file='table/table.tpl'}
					</div>

					{include file='dialog.tpl'}


			</div>



		</div>
	</main>


{include file='admin/footer.tpl'}


<script>

{include file='table/js_1.tpl'}

$("#unblock").click(function () {
        $.ajax({
            type: "POST",
            url: "/admin/unblock",
            dataType: "json",
            data: {
                ip: $$getValue('ip')
            },
            success: data => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
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
});

window.addEventListener('load', () => {
    {include file='table/js_2.tpl'}
});
</script>
