{include file='admin/tabler_header.tpl'}

<style>
    table td {
        white-space: nowrap;
    }
</style>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">工单回复</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里查看历史消息并添加回复</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button href="#" class="btn btn-red d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#close_ticket_confirm_dialog">
                            <i class="icon ti ti-x"></i>
                            关闭
                        </button>
                        <button href="#" class="btn btn-red d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#close_ticket_confirm_dialog">
                            <i class="icon ti ti-x"></i>
                        </button>
                        <button href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <i class="icon ti ti-plus"></i>
                            回复
                        </button>
                        <button href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <i class="icon ti ti-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="h1 my-2 mb-3">#{$topic->tk_id} {$topic->title}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center my-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="divide-y">
                                {$count = '0'}
                                {$total = $discussions->count()}
                                {foreach $discussions as $discuss}
                                    <div>
                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="avatar">用户</span>
                                            </div>
                                            <div class="col">
                                                <div>
                                                    {nl2br($discuss->content)}
                                                </div>
                                                <div class="text-muted my-1">{$discuss->created_at}</div>
                                            </div>
                                            <!-- 标记最新回复 -->
                                            {$count = $count + 1}
                                            {if $count == $total}
                                                <div class="col-auto align-self-center">
                                                    <div class="badge bg-primary"></div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p style="line-height: 24px;">
                                提交用户：<code>{$tk_user->id}</code>
                                ，昵称：<code>{$tk_user->user_name}</code>
                                ，注册邮箱：<code>{$tk_user->email}</code>
                                ，<a href="/admin/user/{$tk_user->id}/edit">编辑用户</a>
                            </p>
                            <p style="line-height: 24px;">
                                用户等级：<code>{$tk_user->class}</code>
                                ，等级时间：<code>{$tk_user->class_expire}</code>
                                ，到期时间：<code>{$tk_user->expire_in}</code>
                                ，流量限制：<code>{round($tk_user->transfer_enable / 1073741824, 2)}</code> GB
                                ，历史用量：<code>{round($tk_user->last_day_t / 1073741824, 2)}</code> GB
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="add-reply" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">添加回复</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea id="reply-content" class="form-control" rows="12" placeholder="请输入回复内容"></textarea>
                    </div>
                    {if $config['quick_fill_function'] === true}
                        <div class="row g-2 align-items-center">
                            {foreach $config['quick_fill_content'] as $item}
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto py-3">
                                    <button id="{$item['id']}" class="btn btn-blue w-100">
                                        {$item['title']}
                                    </button>
                                </div>
                            {/foreach}
                        </div>
                    {/if}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="reply" type="button" class="btn btn-primary" data-bs-dismiss="modal">回复</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="close_ticket_confirm_dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">关闭工单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>
                            确认关闭工单？
                        <p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="confirm_close" type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#reply").click(function() {
            $.ajax({
                url: "/admin/ticket/{$topic->tk_id}",
                type: 'PUT',
                dataType: "json",
                data: {
                    content: $('#reply-content').val()
                },
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

        $("#confirm_close").click(function() {
            $.ajax({
                url: "/admin/ticket/{$topic->tk_id}/close",
                type: 'PUT',
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

        {foreach $config['quick_fill_content'] as $item}
            $("#{$item['id']}").click(function() {
                $("#reply-content").text("{$item['content']}");
            });
        {/foreach}

        $("#success-confirm").click(function() {
            location.reload();
        });
    </script>

{include file='admin/tabler_footer.tpl'}