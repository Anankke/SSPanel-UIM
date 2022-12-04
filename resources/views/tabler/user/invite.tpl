{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">       
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">邀请注册</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">查看邀请注册链接和邀请返利记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">邀请规则</h3>
                            <ul>
                                <li>邀请注册的用户在账单确认后，您可获得其账单金额的 <code>{$config['code_payback'] * 100} %</code>
                                    作为返利</li>
                                <li>具体邀请返利规则请查看公告，或通过工单系统询问管理员</li>
                                <li>部分商品的返利比例可能不遵循上面的比例</li>
                            </ul>
                            <p>您目前通过邀请好友获得的总返利为 <code>{$paybacks_sum}</code> 元</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">邀请链接</h3>
                            {if $user->invite_num >= 0}
                                <p>邀请链接可用次数：<code>{$user->invite_num}</code></p>
                            {/if}
                            <input class="form-control" value="{$invite_url}" disabled />
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="reset-url" class="btn text-red btn-link">重置</a>
                                <a data-clipboard-text="{$invite_url}" class="copy btn btn-primary ms-auto">复制</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">返利记录</h3>
                        </div>
                        {if $paybacks->count() != '0'}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>邀请用户昵称</th>
                                            <th>返利金额</th>
                                            <th>结算审核</th>
                                            <th>返利时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $paybacks as $payback}
                                            <tr>
                                                <td>{$payback->id}</td>
                                                {if $payback->user()!=null}
                                                    <td>{$payback->user()->user_name}</td>
                                                {else}
                                                    <td>已注销</td>
                                                {/if}
                                                <td>{$payback->ref_get} 元</td>
                                                <td>{$payback->fraud_detect}</td>
                                                <td>{$payback->datetime}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="card-body">
                                <p>没有找到记录</p>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("td:contains('通过')").css("color", "green");
        $("td:contains('欺诈')").css("color", "red");

        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });

        $("#reset-url").click(function() {
            $.ajax({
                type: "PUT",
                url: "/user/invite",
                dataType: "json",
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>

{include file='user/tabler_footer.tpl'}