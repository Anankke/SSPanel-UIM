{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container"><h1 class="content-heading">添加中转规则</h1></div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">
                <form id="main_form">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="source_node">起源节点</label>
                                    <button id="source_node" class="form-control maxwidth-edit" name="source_node"
                                            data-toggle="dropdown">
                                        请选择起源节点
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="source_node">
                                        {foreach $source_nodes as $source_node}
                                            <li>
                                                <a href="#" class="dropdown-option" onclick="return false;"
                                                   val="{$source_node->id}" data="source_node">{$source_node->name}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="dist_node">目标节点</label>
                                    <button id="dist_node" class="form-control maxwidth-edit" name="dist_node"
                                            data-toggle="dropdown">
                                        请选择目标节点
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dist_node">
                                        <li>
                                            <a href="#" class="dropdown-option" onclick="return false;" val="-1"
                                               data="dist_node">不进行中转</a>
                                        </li>
                                        {foreach $dist_nodes as $dist_node}
                                            <li>
                                                <a href="#" class="dropdown-option" onclick="return false;"
                                                   val="{$dist_node->id}" data="dist_node">{$dist_node->name}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="port">端口</label>
                                    <button id="port" class="form-control maxwidth-edit" name="port"
                                            data-toggle="dropdown">
                                        请选择端口
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="port">
                                        {foreach $ports as $port}
                                            <li><a href="#" class="dropdown-option" onclick="return false;"
                                                   val="{$port}" data="port">{$port}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="priority">优先级</label>
                                    <input class="form-control maxwidth-edit" id="priority" name="priority" type="text"
                                           value="0">
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
                                            <button id="submit" type="submit" class="btn btn-block btn-brand">添加
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                {include file='dialog.tpl'}
                <section>
        </div>
    </div>
</main>

{include file='user/footer.tpl'}

{literal}
<script>
    $('#main_form').validate({
        rules: {
            priority: {required: true}
        },
        submitHandler: () => {
            $.ajax({
                type: "POST",
                url: "/user/relay",
                dataType: "json",
                {/literal}
                data: {
                    source_node: $$getValue('source_node'),
                    dist_node: $$getValue('dist_node'),
                    port: $$getValue('port'),
                    priority: $$getValue('priority')
                    {literal}
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        {/literal}
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                        {literal}
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${
                            data.msg
                            } 发生错误了`;
                }
            });
        }
    });
</script>
{/literal}