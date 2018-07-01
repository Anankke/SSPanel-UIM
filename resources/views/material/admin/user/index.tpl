{include file='admin/main.tpl'}

<style>
	table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
	    content: ""!important;
	}
	table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
	    content: ""!important;
</style>





<main class="content">
	<div class="content-header ui-content-header">
		<div class="container">
			<h1 class="content-heading">用户列表</h1>
		</div>
	</div>
	<div class="container">
		<div class="col-lg-12 col-sm-12">
			<section class="content-inner margin-top-no">

				<div class="card">
					<div class="card-main">
						<div class="card-inner">
							<p>系统中所有用户的列表。</p>
							<p>显示表项:
				                {include file='table/checkbox.tpl'}
			              	</p>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					{include file='table/table.tpl'}
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
				<div aria-hidden="true" class="modal modal-va-middle fade" id="changetouser_modal" role="dialog" tabindex="-1">
					<div class="modal-dialog modal-xs">
						<div class="modal-content">
							<div class="modal-heading">
								<a class="modal-close" data-dismiss="modal">×</a>
								<h2 class="modal-title">确认要切换此用户？</h2>
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
				{include file='dialog.tpl'}


		</div>



	</div>
</main>






{include file='admin/footer.tpl'}

<script>
function delete_modal_show(id) {
	deleteid=id;
	$("#delete_modal").modal();
}
function changetouser_modal_show(id) {
	changetouserid=id;
	$("#changetouser_modal").modal();
}
{include file='table/js_1.tpl'}

$(document).ready(function(){
	{include file='table/js_2.tpl'}

	function delete_id(){
		$.ajax({
			type:"DELETE",
			url:"/admin/user",
			dataType:"json",
			data:{
				id: deleteid
			},
			success:function(data){
				if(data.ret){
					$("#result").modal();
					$("#msg").html(data.msg);
					{include file='table/js_delete.tpl'}
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
	function changetouser_id(){
		$.ajax({
			type:"POST",
			url:"/admin/user/changetouser",
			dataType:"json",
			data:{
              userid: changetouserid,
              adminid: {$user->id},
              local: '/admin/user'
			},
			success:function(data){
				if(data.ret){
					$("#result").modal();
					$("#msg").html(data.msg);
                    window.setTimeout("location.href='/user'", {$config['jump_delay']});
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
	$("#changetouser_input").click(function(){
		changetouser_id();
	});
})


</script>
s
