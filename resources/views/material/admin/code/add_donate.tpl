{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">添加捐赠或支出记录</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">类型</label>
                                <select id="type" class="form-control maxwidth-edit" name="type">
                                    <option value="-1">捐赠</option>
                                    <option value="-2">支出</option>
                                </select>
                            </div>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="number">备注</label>
                                <input class="form-control maxwidth-edit" id="code" type="text">
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="amount">金额</label>
                                <input class="form-control maxwidth-edit" id="amount" type="text">
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
                                                class="btn btn-block btn-brand waves-attach waves-light">添加
                                        </button>
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


<script>
    window.addEventListener('load', () => {
        let submit = () => {
            $.ajax({
                type: "POST",
                url: "/admin/donate",
                dataType: "json",
                data: {
                    amount: $$getValue("amount"),
                    code: $$getValue("code"),
                    type: $$getValue("type")
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                    } else {
                        $("#msg-error").hide(10);
                        $("#msg-error").show(100);
                        $$.getElementById('msg-error-p').innerHTML = data.msg;
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
            if (event.keyCode === 13) {
                login();
            }
        });

        $$.getElementById('submit').addEventListener('click', submit);

    })
</script>
