{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">充值</h1>


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
                                    <p class="card-heading">注意!</p>
                                    <p>充值完成后需刷新网页以查看余额，通常一分钟内到账。</p>
                                    <p>因余额不足而未能完成的自动续费，在余额足够时会自动划扣续费。</p>
                                    {if $config['enable_admin_contact'] === true}
                                        <p class="card-heading">如果没有到账请立刻联系管理员：</p>
                                        {if $config['admin_contact1']!=''}
                                            <li>{$config['admin_contact1']}</li>
                                        {/if}
                                        {if $config['admin_contact2']!=''}
                                            <li>{$config['admin_contact2']}</li>
                                        {/if}
                                        {if $config['admin_contact3']!=''}
                                            <li>{$config['admin_contact3']}</li>
                                        {/if}
                                    {/if}
                                    <br/>
                                    <p><i class="icon icon-lg">attach_money</i>当前余额：<font color="#399AF2" size="5">{$user->money}</font> 元</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {if $pmw!=''}
                    <div class="col-lg-12 col-md-12">
                        <div class="card margin-bottom-no">
                            <div class="card-main">
                                <div class="card-inner">
                                    {$pmw}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                {if $bitpay!=''}
                    <div class="col-lg-12 col-md-12">
                        <div class="card margin-bottom-no">
                            <div class="card-main">
                                <div class="card-inner">
                                    {$bitpay}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <div class="cardbtn-edit">
                                        <div class="card-heading">充值码</div>
                                        <button class="btn btn-flat" id="code-update">
                                            <span class="icon">favorite_border</span>
                                        </button>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="code">充值码</label>
                                        <input class="form-control maxwidth-edit" id="code" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">

                                <div class="card-table">
                                    <div class="table-responsive table-user">
                                        {$codes->render()}
                                        <table class="table table-hover">
                                            <tr>
                                                <!--<th>ID</th> -->
                                                <th>代码</th>
                                                <th>类型</th>
                                                <th>操作</th>
                                                <th>使用时间</th>

                                            </tr>
                                            {foreach $codes as $code}
                                                {if $code->type!=-2}
                                                    <tr>
                                                        <!--	<td>#{$code->id}</td>  -->
                                                        <td>{$code->code}</td>
                                                        {if $code->type==-1}
                                                            <td>金额充值</td>
                                                        {/if}
                                                        {if $code->type==10001}
                                                            <td>流量充值</td>
                                                        {/if}
                                                        {if $code->type==10002}
                                                            <td>用户续期</td>
                                                        {/if}
                                                        {if $code->type>=1&&$code->type<=10000}
                                                            <td>等级续期 - 等级{$code->type}</td>
                                                        {/if}
                                                        {if $code->type==-1}
                                                            <td>充值 {$code->number} 元</td>
                                                        {/if}
                                                        {if $code->type==10001}
                                                            <td>充值 {$code->number} GB 流量</td>
                                                        {/if}
                                                        {if $code->type==10002}
                                                            <td>延长账户有效期 {$code->number} 天</td>
                                                        {/if}
                                                        {if $code->type>=1&&$code->type<=10000}
                                                            <td>延长等级有效期 {$code->number} 天</td>
                                                        {/if}
                                                        <td>{$code->usedatetime}</td>
                                                    </tr>
                                                {/if}
                                            {/foreach}
                                        </table>
                                        {$codes->render()}
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal modal-va-middle fade" id="readytopay" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">正在连接支付网关</h2>
                            </div>
                            <div class="modal-inner">
                                <p id="title">感谢您对我们的支持，请耐心等待</p>
                            </div>
                        </div>
                    </div>
                </div>

                {include file='dialog.tpl'}
            </div>
        </section>
    </div>
</main>
<script>
    $(document).ready(function () {
        $("#code-update").click(function () {
            $.ajax({
                type: "POST",
                url: "code",
                dataType: "json",
                data: {
                    code: $$getValue('code')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
{literal}
                    $$.getElementById('msg').innerHTML = `发生错误：${jqXHR.status}`;
{/literal}
                }
            })
        })
    })
</script>

{include file='user/footer.tpl'}
