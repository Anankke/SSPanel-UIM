


{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">中转链路搜索</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
				<section class="content-inner margin-top-no">

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>系统中这位用户的链路。</p>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="form-group form-group-label">
									<label class="floating-label" for="search"> 输入用户 ID 进行搜索链路搜索 </label>
									<input class="form-control" id="search" type="text">
								</div>
							</div>
							<div class="card-action">
								<div class="card-action-btn pull-left">
									<a class="btn btn-flat waves-attach waves-light" id="search_button"><span class="icon">search</span>&nbsp;搜索</a>
								</div>
							</div>
						</div>
					</div>

          <div class="table-responsive">
            <table class="mdl-data-table" id="table_1" cellspacing="0" width="100%">
							<thead>
	              <tr>
	              <th>端口</th>
	              <th>始发节点</th>
	              <th>终点节点</th>
	              <th>途径节点</th>
	              <th>状态</th>
	              </tr>
							</thead>
							<tfoot>
	              <tr>
	              <th>端口</th>
	              <th>始发节点</th>
	              <th>终点节点</th>
	              <th>途径节点</th>
	              <th>状态</th>
	              </tr>
							</tfoot>
							<tbody>
	              {foreach $pathset as $path}
	              <tr>
	              <td>{$path->port}</td>
	              <td>{$path->begin_node->name}</td>
	              <td>{$path->end_node->name}</td>
	              <td>{$path->path}</td>
	              <td>{$path->status}</td>
	              </tr>
	              {/foreach}
							</tbody>
            </table>
          </div>


			</div>



		</div>
	</main>






{include file='admin/footer.tpl'}




<script>


$(document).ready(function(){
 	table = $('#table_1').DataTable({
		"columnDefs": [
			{
					targets: [ '_all' ],
					className: 'mdl-data-table__cell--non-numeric'
			}
		],
		{include file='table/lang_chinese.tpl'}
	});

	function search(){
		window.location="/admin/relay/path_search/"+$("#search").val();
	}

	$("#search_button").click(function(){
		if($("#search").val()!="")
		{
			search();
		}
	});
})

</script>
