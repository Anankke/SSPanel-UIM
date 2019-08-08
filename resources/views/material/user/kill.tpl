{include file='user/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">删除我的帐号</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="row">

                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <h4>注意！</h4>

                                    <p>帐号删除后，您的所有数据都会被<b>真实地</b>删除。</p>

                                    <p>如果想重新使用本网站提供的服务，您需要重新注册。</p>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <p class="card-heading">输入当前密码以验证身份</p>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="passwd">当前密码（必填）</label>
                                        <input class="form-control" id="passwd" type="password">
                                    </div>
                                </div>
                                <div class="card-action">
                                    <div class="card-action-btn pull-left">
                                        <button class="btn btn-flat waves-attach" id="kill"><span
                                                    class="icon">check</span>&nbsp;删除我的帐号
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {include file='dialog.tpl'}
            </div>
        </section>
    </div>
</main>


{include file='user/footer.tpl'}


<script>
    $(document).ready(function () {
        $("#kill").click(function () {
            $.ajax({
                type: "POST",
                url: "kill",
                dataType: "json",
                data: {
                    passwd: $$getValue('passwd'),
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            } ${
                            data.msg
                            }`;
                }
            })
        })
    })
</script>

