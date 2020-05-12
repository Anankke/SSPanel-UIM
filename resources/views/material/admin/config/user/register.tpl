{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">注册设置</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="form-group form-group-label">
                                <input class="form-control maxwidth-edit" id="name" type="text" value="{$edit_config->name}" readonly>
                            </div>
                            <div class="form-group form-group-label">
                                <label for="value">
                                    <select id="value" class="form-control maxwidth-edit" name="value">
                                        {$value = $edit_config->getValue()}
                                        <option value="open" {if $value == 'open'}selected{/if}>开启</option>
                                        <option value="close" {if $value == 'close'}selected{/if}>关闭</option>
                                        <option value="invite" {if $value == 'invite'}selected{/if}>仅限邀请码</option>
                                    </select>
                                </label>
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
                                        <button id="submit" type="submit"
                                                class="btn btn-block btn-brand waves-attach waves-light">修改
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>显示表项: {include file='table/checkbox.tpl'}</p>
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

    window.addEventListener('load', () => {
        {include file='table/js_2.tpl'}
    });
</script>

<script>
    window.addEventListener('load', () => {
        function submit() {
            $.ajax({
                type: "PUT",
                url: "/admin/config/update/{$edit_config->key}",
                dataType: "json",
                data: {
                    value: $$getValue('value')
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
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            });
        }

        $("html").keydown(event => {
            if (event.keyCode == 13) {
                submit();
            }
        });

        $$.getElementById('submit').addEventListener('click', submit);
    })
</script>
