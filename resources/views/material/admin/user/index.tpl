{include file='admin/main.tpl'}





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
 	table_1 = $('#table_1').DataTable({
			order:[[1, 'asc' ]],
			stateSave: true,
			serverSide: true,
			ajax: {
				url :"/admin/user/ajax",
                type: "POST",
			},			
			columns: [
				{ "data": "op" ,"orderable":false},
				{ "data": "id" },
				{ "data": "user_name" },
				{ "data": "remark" },
				{ "data": "email" },
				{ "data": "money" },
				{ "data": "im_type" },
				{ "data": "im_value" },
				{ "data": "node_group" },
				{ "data": "expire_in" },
				{ "data": "class" },
				{ "data": "class_expire" },
				{ "data": "passwd" },
				{ "data": "port" },
				{ "data": "method" },
				{ "data": "protocol" },
				{ "data": "obfs" },
				{ "data": "online_ip_count" ,"orderable":false},
				{ "data": "last_ss_time" ,"orderable":false},
				{ "data": "used_traffic" ,"orderable":false},
				{ "data": "enable_traffic" ,"orderable":false},
				{ "data": "last_checkin_time" ,"orderable":false},
				{ "data": "today_traffic" ,"orderable":false},
				{ "data": "enable" },
				{ "data": "reg_date" },
				{ "data": "reg_ip" },
				{ "data": "auto_reset_day" },
				{ "data": "auto_reset_bandwidth" },
				{ "data": "ref_by" },
				{ "data": "ref_by_user_name" ,"orderable":false}
			],
			"columnDefs": [
				{
					targets: [ '_all' ],
					className: 'mdl-data-table__cell--non-numeric'
				}
			],
			{include file='table/lang_chinese.tpl'}
  });

	var has_init = JSON.parse(localStorage.getItem(window.location.href + '-hasinit'));
	if (has_init != true) {
	    localStorage.setItem(window.location.href + '-hasinit', true);
	} else {
	    {foreach $table_config['total_column'] as $key => $value}
	        var checked = JSON.parse(localStorage.getItem(window.location.href + '-haschecked-checkbox_{$key}'));
	        if (checked == true) {
	            document.getElementById('checkbox_{$key}').checked = true;
	        } else {
	            document.getElementById('checkbox_{$key}').checked = false;
	        }
	    {/foreach}
	}

	{foreach $table_config['total_column'] as $key => $value}
	  modify_table_visible('checkbox_{$key}', '{$key}');
	{/foreach}

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
	
	$("#search_button").click(function(){
		if($("#search").val()!="")
		{
			search();
		}
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