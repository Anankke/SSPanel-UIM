{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading"> 添加规则</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">
                <form id="main_form">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="name">规则名称</label>
                                    <input class="form-control maxwidth-edit" id="name" name="name" type="text">
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="text">规则描述</label>
                                    <input class="form-control maxwidth-edit" id="text" name="text" type="text">
                                </div>

                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="regex">规则正则表达式</label>
                                    <input class="form-control maxwidth-edit" id="regex" name="regex" type="text">
                                </div>

                                <div class="form-group form-group-label">
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="type">规则类型</label>
                                        <select id="type" class="form-control maxwidth-edit" name="type">
                                            <option value="1">数据包明文匹配</option>
                                            <option value="2">数据包 hex 匹配</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group form-group-label">
                                    <button id="submit" type="submit" class="btn btn-block btn-brand waves-attach waves-light">添加</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                {include file='dialog.tpl'}

        </div>

    </div>
</main>

{include file='admin/footer.tpl'}


<script>

    {literal}
    $('#main_form').validate({
        rules: {
            name: {required: true},
            text: {required: true},
            regex: {required: true}
        },
        {/literal}
        submitHandler: function () {
            {literal}
            $.ajax({
                type: "POST",
                url: "/admin/detect",
                dataType: "json",
                data: {
                    name: $$getValue("name"),
                    text: $$getValue("text"),
                    regex: $$getValue("regex"),
                    type: $$getValue("type")
                },
                {/literal}
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
                error: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${ldelim}data.msg{rdelim} 发生错误了。`;
                }
            });
        }
    });

</script>

