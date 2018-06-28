


{include file='user/main.tpl'}







    <main class="content">
    
        <div class="content-header ui-content-header">
            <div class="container">
                <h1 class="content-heading">节点编辑</h1>
            </div>
        </div>
        
        <div class="container">
        <section class="content-inner margin-top-no">

{if $nodes ==0}
                        <div class="card margin-bottom-no">
                            <div class="card-main">
                                <div class="card-inner">
                                    <div class="card-inner">
                                        <p class="card-heading">暂无相关节点</p>
                                    </div>
                                </div>
                            </div>
                        </div>
    {include file='dialog.tpl'}

        </section>
        </div>

    </main>

    {include file='user/footer.tpl'}

{else}

                        <div class="card margin-bottom-no">
                            <div class="card-main">
                                <div class="card-inner">
                                    <div class="card-inner">
                                        <p class="card-heading">修改须知</p>
                                        <p>若是设置了中转，则加密、混淆、协议和密码等等都以终点服务器为准！</p>
                                    </div>
                                </div>
                            </div>
                        </div>

    {foreach $usermethods as $usermethod}
    {foreach $nodes as $node}
    {if $usermethod->node_id == $node->id}

                        <div class="card margin-bottom-no">
                            <div class="card-main">

                                <div class="card-inner">

                                        <p class="card-heading">{$node->name}</p>
                                        <div class="card-action-btn pull-left" style="width:100%">
                                        <p>当前端口：{$usermethod->port}    <button class="btn btn-flat waves-attach" id="ssr-node-{$node->id}-port" ><span class="icon">check</span>&nbsp;重置端口</button></p>
                                            
                                        </div>
                                        <div class="form-group form-group-label" style="display:none">
                                            <select id="node_id-node-{$node->id}" class="form-control">
                                            <option value="{$node->id}" >{$node->name}</option>
                                            </select>
                                        </div>

                                        <div class="form-group form-group-label" style="float:left;width:220px">
                                            <label class="floating-label" for="sspwd">连接密码 当前密码：{$usermethod->passwd}</label>
                                            <input class="form-control" id="sspwd-node-{$node->id}" type="password" value="{$usermethod->passwd}">
                                        </div>
                                        
                                        <div class="form-group form-group-label" style="float:left;width:220px">
                                            <label class="floating-label" for="method">加密方式</label>
                                            <select id="method-node-{$node->id}" class="form-control">
                                            {foreach $method_list as $method}   <option value="{$method}" {if $usermethod->method == $method}selected="selected"{/if}>[{if URL::CanMethodConnect($method) == 2}SS{else}SS/SSR{/if} 可连接] {$method}</option>
                                            {/foreach}</select>
                                        </div>
                                        <div class="form-group form-group-label" style="float:left;width:220px">
                                            <label class="floating-label" for="protocol">协议</label>
                                            <select id="protocol-node-{$node->id}" class="form-control">
                                            {foreach $protocol_list as $protocol}   <option value="{$protocol}" {if $usermethod->protocol == $protocol}selected="selected"{/if}>[{if URL::CanProtocolConnect($protocol) == 3}SS/SSR{else}SSR{/if} 可连接] {$protocol}</option>
                                            {/foreach}</select>
                                        </div>
                                        <div class="form-group form-group-label" style="float:left;width:220px">
                                            <label class="floating-label" for="obfs">混淆</label>
                                            <select id="obfs-node-{$node->id}" class="form-control">
                                            {foreach $obfs_list as $obfs}   <option value="{$obfs}" {if $usermethod->obfs == $obfs}selected="selected"{/if}>[{if URL::CanObfsConnect($obfs) >= 3}SS/SSR{else}{if URL::CanObfsConnect($obfs) == 1}SSR{else}SS{/if}{/if} 可连接] {$obfs}</option>
                                            {/foreach}</select>
                                        </div>

                                </div>
                                <div class="card-action">
                                    <div class="card-action-btn pull-left">
                                        <button class="btn btn-flat waves-attach" id="ssr-node-{$node->id}" ><span class="icon">check</span>&nbsp;提交</button>
                                    </div>
                                </div>
                                    
                            </div>
                        </div>
    {/if}
    {/foreach}
    {/foreach}

    {include file='dialog.tpl'}

        </section>
        </div>

    </main>

    {include file='user/footer.tpl'}

    {foreach $nodes as $node}
    <script>
        $(document).ready(function () {
            $("#ssr-node-{$node->id}").click(function () {
                $.ajax({
                    type: "POST",
                    url: "usermethod",
                    dataType: "json",
                    data: {
                        node_id: $("#node_id-node-{$node->id}").val(),
                        method: $("#method-node-{$node->id}").val(),
                        sspwd: $("#sspwd-node-{$node->id}").val(),
                        protocol: $("#protocol-node-{$node->id}").val(),
                        obfs: $("#obfs-node-{$node->id}").val()
                    },
                    success: function (data) {
                        if (data.ret) {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                        } else {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                        }
                    },
                    error: function (jqXHR) {
                        $("#result").modal();
                        $("#msg").html(data.msg+"     出现了一些错误。");
                    }
                })
            })
        })
    </script>
    <script>
        $(document).ready(function () {
            $("#ssr-node-{$node->id}-port").click(function () {
                $.ajax({
                    type: "POST",
                    url: "usermethodport",
                    dataType: "json",
                    data: {
                        node_id: $("#node_id-node-{$node->id}").val(),
                        port: $("#port-node-{$node->id}").val()
                    },
                    success: function (data) {
                        if (data.ret) {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                        } else {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                        }
                    },
                    error: function (jqXHR) {
                        $("#result").modal();
                        $("#msg").html(data.msg+"     出现了一些错误。");
                    }
                })
            })
        })
    </script>
    {/foreach}

{/if}
